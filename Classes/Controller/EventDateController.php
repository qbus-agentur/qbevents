<?php
namespace Qbus\Qbevents\Controller;

use Qbus\Qbevents\Domain\Model\EventDate;
use Qbus\Qbevents\Domain\Repository\EventDateRepository;
use TYPO3\CMS\Extbase\Persistence\QueryResultInterface;

/**
 * EventDateController
 *
 * @author Benjamin Franzke <bfr@qbus.de>
 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License, version 3 or later
 */
class EventDateController extends \TYPO3\CMS\Extbase\Mvc\Controller\ActionController
{
    /**
     * @var EventDateRepository
     */
    protected $eventDateRepository;

    /**
     * @param  EventDateRepository $eventDateRepository
     * @return void
     */
    public function injectEventDateRepository(EventDateRepository $eventDateRepository)
    {
        $this->eventDateRepository = $eventDateRepository;
    }

    /**
     * list
     *
     * @param  array $demands
     * @return void
     */
    public function listAction($demands = null)
    {
        $this->redirectIfPost(['demands' => $demands]);

        $signalArguments = $this->signalSlotDispatcher->dispatch(__CLASS__, 'listAction_pre', array(
            'demands'  => $demands,
            'settings' => $this->settings,
        ));
        $demands        = $signalArguments['demands'];
        $this->settings = $signalArguments['settings'];

        if (!$demands) {
            $demands = [];
        }
        $eventDemands = array();

        if (isset($this->settings['demands']) && is_array($this->settings['demands']) && count($this->settings['demands']) > 0) {
            $eventDemands = $this->settings['demands'];
        }

        $signalArguments = $this->signalSlotDispatcher->dispatch(__CLASS__, 'listAction_demands', array(
            'demands'      => $demands,
            'eventDemands' => $eventDemands,
            'settings'     => $this->settings,
        ));
        $demands        = $signalArguments['demands'];
        $eventDemands   = $signalArguments['eventDemands'];
        $this->settings = $signalArguments['settings'];

        $limit = isset($this->settings['demands_limit']) ? $this->settings['demands_limit'] : 0;

        if (isset($this->settings['disableUpcomingRestriction'])) {
            $dates = $this->eventDateRepository->find($eventDemands, $limit);
        } else {
            $dates = $this->eventDateRepository->findUpcoming($eventDemands, $limit);
        }

        $variables = [
            'dates' => $dates,
            'demands' => $demands,
            'extended' => [],
        ];
        $variables = $this->signalSlotDispatcher->dispatch(__CLASS__, 'listAction_variables', $variables);

        $this->view->assignMultiple($variables);

        if (isset($this->settings['template']) && $this->settings['template']) {
            if (!method_exists($this->view, 'setTemplatePathAndFilename')) {
                throw new \Exception('View ' . get_class($this->view) . ' does not support template override', 1552556765);
            }
            $this->view->setTemplatePathAndFilename($this->settings['template']);
        }

        $GLOBALS['TSFE']->addCacheTags([
            'tx_qbevents_domain_model_event',
            'tx_qbevents_domain_model_eventdate',
        ]);

        // Update possibly modified settings into the view –  the modified settings are not updated automatically,
        // therefore we assign them here.
        if (method_exists($this->view, 'injectSettings')) {
            $this->view->injectSettings($this->settings);
        }
        // In TYPO3.Flow, solved through Object Lifecycle methods, we need to call it explicitly
        $this->view->assign('settings', $this->settings);
    }

    /**
     * show
     *
     * @param EventDate $date
     *
     * @return void
     */
    public function showAction(EventDate $date)
    {
        $variables = [
            'date' => $date,
            'extended' => [],
        ];
        $variables = $this->signalSlotDispatcher->dispatch(__CLASS__, 'showAction_variables', $variables);
        $this->view->assignMultiple($variables);

        $GLOBALS['TSFE']->addCacheTags([
            'tx_qbevents_domain_model_event',
            'tx_qbevents_domain_model_eventdate',
        ]);
    }

    /**
     * teaser
     */
    public function teaserAction()
    {
        $demands = array();

        if (isset($this->settings['demands']) && is_array($this->settings['demands']) && count($this->settings['demands']) > 0) {
            $demands = $this->settings['demands'];
        }

        $dates = $this->eventDateRepository->findUpcoming($demands, 1);

        $date = $dates->getFirst();

        $variables = [
            /* Heads UP! $date may be null */
            'date' => $date,
            'contentObject' => $this->configurationManager->getContentObject()->data,
            'extended' => [],
        ];
        $variables = $this->signalSlotDispatcher->dispatch(__CLASS__, 'teaserAction_variables', $variables);
        $this->view->assignMultiple($variables);

        $GLOBALS['TSFE']->addCacheTags(array(
            'tx_qbevents_domain_model_event',
            'tx_qbevents_domain_model_eventdate',
        ));
    }

    /**
     * calendar
     *
     * @param string $date
     */
    public function calendarAction($date = null)
    {
        $start = new \DateTime($date);
        $start->modify('first day of this month')->setTime(0, 0, 0);
        $end = clone $start;
        $end->modify('last day of this month')->setTime(23, 59, 59);

        $weeks = $this->collectCalendarData($start, $end);
        $this->view->assign('weeks', $weeks);

        $next = clone $start;
        $next->modify('first day of next month');
        $prev = clone $end;
        $prev->modify('first day of last month');

        $this->view->assign('month', $start);
        $this->view->assign('prev', $prev);
        $this->view->assign('next', $next);

        $this->view->assign('contentObject', $this->configurationManager->getContentObject()->data);
    }

    protected function getWeekNumber(\DateTime $date) {
        $tmp = clone $date;
        $tmp->modify('first day of this month');

        $offset = (int) ($tmp->format('N')) - 1;
        return ceil(($date->format('d') + $offset)/7);
    }

    protected function collectCalendarData(\DateTime $start, \DateTime $end)
    {
        $demands = [
            [
                'demand' => [
                    'operation' => 'GREATERTHANOREQUAL',
                    'property' => 'start',
                    'value' => $start,
                ]
            ],
            [
                'demand' => [
                    'operation' => 'LESSTHANOREQUAL',
                    'property' => 'end',
                    'value' => $end,
                ]
            ],
            [
                'demand' => [
                    'operation' => 'EQUALS',
                    'property' => 'frequency',
                    'value' => '0',
                ]
            ],
        ];
        $dates = $this->eventDateRepository->find($demands);
        $weeks = [
        ];

        $startDay = (int) $start->format('N');
        for ($i = 1; $i < $startDay; ++$i) {
            $weeks[1][$i] = null;
        }

        $period = new \DatePeriod($start, new \DateInterval('P1D'), $end);
        $today = new \DateTime;
        foreach ($period as $date) {
            $week = $this->getWeekNumber($date);
            $weekday = (int) $date->format('N');
            $day = (int) $date->format('d');
            $weeks[$week][$weekday] = [
                'day' => $day,
                'today' => $today->format('Y-m-d') === $date->format('Y-m-d'),
                'dates' => [],
            ];
        }

        foreach ($dates as $date) {
            $week = $this->getWeekNumber($date->getStart());
            $weekday = (int) $date->getStart()->format('N');
            $weeks[$week][$weekday]['dates'][] = $date;
        }

        return $weeks;
    }

    protected function redirectIfPost($arguments = array(), $action = null)
    {
        if ($this->request instanceof \TYPO3\CMS\Extbase\Mvc\Web\Request && $this->request->getMethod() === 'POST') {
            $uri = $this->uriBuilder->reset()->setUseCacheHash(false)->uriFor($action, $arguments, null, null, null);

            header('HTTP/1.1 303 See Other');
            header("Location: $uri");
            exit;
        }
    }
}
