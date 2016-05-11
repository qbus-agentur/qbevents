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
     * @param array $demands
     * @return void
     */
    public function listAction($demands = NULL)
    {
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

        $dates = $this->eventDateRepository->findUpcoming($eventDemands, $limit);

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
}
