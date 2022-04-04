<?php
namespace Qbus\Qbevents\Controller;

use TYPO3\CMS\Extbase\Mvc\Controller\ActionController;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ResponseFactoryInterface;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Frontend\Controller\ErrorController;
use TYPO3\CMS\Core\Http\ImmediateResponseException;
use TYPO3\CMS\Extbase\Mvc\Request;
use Qbus\Qbevents\Domain\Model\Event;
use Qbus\Qbevents\Domain\Model\EventDate;
use Qbus\Qbevents\Domain\Repository\EventDateRepository;
use TYPO3\CMS\Frontend\Page\PageAccessFailureReasons;

/**
 * EventDateController
 *
 * @author Benjamin Franzke <bfr@qbus.de>
 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License, version 3 or later
 */
class EventDateController extends ActionController
{
    /**
     * @var EventDateRepository
     */
    protected $eventDateRepository;

    /**
     * @var ResponseFactoryInterface
     */
    protected $responseFactory;

    public function injectEventDateRepository(EventDateRepository $eventDateRepository)
    {
        $this->eventDateRepository = $eventDateRepository;
    }


    public function injectResponseFactory(ResponseFactoryInterface $responseFactory)
    {
        $this->responseFactory = $responseFactory;
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
     * @param Event $event
     *
     * @return void
     */
    public function showAction(EventDate $date = null, Event $event = null)
    {
        if ($date === null && $event !== null) {
            $current_date = new \DateTime();
            $redirect_to = null;

            foreach ($event->getDates() as $date) {
                if ($date->getStart() > $current_date) {
                    $redirect_to = $date;
                    break;
                }
            }

            if (!$redirect_to) {
                /* Pick the last one, if none is in future */
                foreach ($event->getDates() as $date) {
                    $redirect_to = $date;
                }
            }
            if (!$redirect_to) {
                $response = GeneralUtility::makeInstance(ErrorController::class)->pageNotFoundAction($GLOBALS['TYPO3_REQUEST'], 'Event not found.', ['code' => PageAccessFailureReasons::PAGE_NOT_FOUND]);
                throw new ImmediateResponseException($response);
                exit;
            }

            $uri = $this->uriBuilder->reset()->setUseCacheHash(true)->uriFor('show', ['date' => $redirect_to], null, null, null);

            header('HTTP/1.1 303 See Other');
            header("Location: $uri");
            exit;
        }

        /* ->event may be hidden, return 404 in that case */
        if ($date === null || $date->getEvent() == null) {
            $response = GeneralUtility::makeInstance(ErrorController::class)->pageNotFoundAction($GLOBALS['TYPO3_REQUEST'], 'Event not found.', ['code' => PageAccessFailureReasons::PAGE_NOT_FOUND]);
            throw new ImmediateResponseException($response);
        }

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
     * @param EventDate $date
     * @return string
     */
    public function icalAction(EventDate $date)
    {
        /* ->event may be hidden, return 404 in that case */
        if ($date->getEvent() === null) {
            $response = GeneralUtility::makeInstance(ErrorController::class)->pageNotFoundAction($GLOBALS['TYPO3_REQUEST'], 'Event not found.', ['code' => PageAccessFailureReasons::PAGE_NOT_FOUND]);
            throw new ImmediateResponseException($response);
        }
        $siteUrl = $GLOBALS['TYPO3_REQUEST']->getAttribute('normalizedParams')->getSiteUrl();
        $vCalendar = new \Eluceo\iCal\Component\Calendar($siteUrl);
        $vEvent = new \Eluceo\iCal\Component\Event();
        $vEvent
            ->setDtStart($date->getStart())
            ->setDtEnd($date->getEnd())
            ->setSummary($date->getEvent()->getTitle());
        $vCalendar->addComponent($vEvent);

        $filename = str_replace(['.', ',', ' '], '_', preg_replace('/[[:^print:]]/', '', $date->getEvent()->getTitle())) . '_' . $date->getStart()->format('Y-m-d') . '.ics';
        $response = $this->responseFactory->createResponse()
            ->withHeader('Content-Type', 'text/calendar; charset=utf-8')
            ->withHeader('Content-Disposition', 'attachment; filename="' . $filename . '"');
        $response->getBody()->write($vCalendar->render());
        throw new ImmediateResponseException($response);
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

        if (!$this->hasEventsOrAnySuccessive($next, true)) {
            $next = null;
        }

        $prevLastDay = clone $prev;
        $prevLastDay->modify('last day of this month');
        if (!$this->hasEventsOrAnySuccessive($prevLastDay, false)) {
            $prev = null;
        }

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

    protected function hasEventsOrAnySuccessive(\DateTime $date, bool $later = true)
    {
        $demands = [
            [
                'demand' => [
                    'operation' => $later ? 'GREATERTHANOREQUAL' : 'LESSTHANOREQUAL',
                    'property' => 'start',
                    'value' => $date,
                ],
            ],
            [
                'demand' => [
                    'operation' => 'GREATERTHAN',
                    'property' => 'start',
                    'value' => 0,
                ],
            ],
            [
                'demand' => [
                    'operation' => 'EQUALS',
                    'property' => 'frequency',
                    'value' => '0',
                ],
            ],
        ];
        $dates = $this->eventDateRepository->find($demands);
        return count($dates) > 0;
    }

    protected function collectCalendarData(\DateTime $start, \DateTime $end)
    {
        $demands = [
            [
                'demand' => [
                    'operation' => 'AND',
                    'operands' => [
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
                                'property' => 'start',
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
                    ],
                ],
            ],
            [
                'demand' => [
                    'operation' => 'AND',
                    'operands' => [
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
                                'operation' => 'GREATHERTHAN',
                                'property' => 'end',
                                'value' => 0,
                            ]
                        ],
                        [
                            'demand' => [
                                'operation' => 'EQUALS',
                                'property' => 'frequency',
                                'value' => '0',
                            ]
                        ],
                    ],
                ],
            ],
        ];
        $dates = $this->eventDateRepository->find($demands, 0, false, 'OR');
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
        if ($this->request instanceof Request && $this->request->getMethod() === 'POST') {
            $uri = $this->uriBuilder->reset()->setUseCacheHash(false)->uriFor($action, $arguments, null, null, null);

            header('HTTP/1.1 303 See Other');
            header("Location: $uri");
            exit;
        }
    }
}
