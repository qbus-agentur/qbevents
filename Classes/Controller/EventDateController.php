<?php
namespace Qbus\Qbevents\Controller;

use Qbus\Qbevents\Domain\Model\EventDate;
use Qbus\Qbevents\Domain\Repository\EventDateRepository;

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
            $this->view->setTemplatePathAndFilename($this->settings['template']);
        }

        $GLOBALS['TSFE']->addCacheTags([
            'tx_qbevents_domain_model_event',
            'tx_qbevents_domain_model_eventdate',
        ]);
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

        $date = null;
        if ($dates instanceof \TYPO3\CMS\Extbase\Persistence\QueryResultInterface) {
            $date = $dates->getFirst();
        } elseif (is_array($dates)) {
            $date = isset($dates[0]) ? $dates[0] : null;
        }

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

    protected function redirectIfPost($arguments = array(), $action = null)
    {
        if ($this->request->getMethod() === 'POST') {
            $uri = $this->uriBuilder->reset()->setUseCacheHash(false)->uriFor($action, $arguments, null, null, null);

            header('HTTP/1.1 303 See Other');
            header("Location: $uri");
            exit;
        }
    }
}
