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
     * @return void
     */
    public function listAction()
    {
        $eventDates = $this->eventDateRepository->findUpcoming();

        $this->view->assign('dates', $eventDates);
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
        $this->view->assign('date', $date);
        $GLOBALS['TSFE']->addCacheTags([
            'tx_qbevents_domain_model_event',
            'tx_qbevents_domain_model_eventdate',
        ]);
    }
}
