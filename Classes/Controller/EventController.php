<?php
namespace Qbus\Qbevents\Controller;

use Qbus\Qbevents\Domain\Model\Event;
use Qbus\Qbevents\Domain\Repository\EventRepository;

/**
 * EventController
 *
 * @author Benjamin Franzke <bfr@qbus.de>
 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License, version 3 or later
 */
class EventController extends \TYPO3\CMS\Extbase\Mvc\Controller\ActionController
{
    /**
     * @var EventRepository
     */
    protected $eventRepository;

    /**
     * @param  EventRepository $eventRepository
     * @return void
     */
    public function injectEventRepository(EventRepository $eventRepository)
    {
        $this->eventRepository = $eventRepository;
    }

    public function listAction()
    {
        $findUpcoming = false;
        $orderby = null;

        if (isset($this->settings['upcoming']) && $this->settings['upcoming']) {
            $findUpcoming = true;
        }
        if (isset($this->settings['orderby']) && $this->settings['orderby']) {
            $orderby = $this->settings['orderby'];
        }

        $demands = $this->settings['demands'];
        $limit = isset($this->settings['demands_limit']) ? $this->settings['demands_limit'] : 0;

        if ($findUpcoming) {
            if (!is_array($demands)) {
                $demands = [];
            }
            $demands[] = [
                'demand' => [
                    'operation' => 'GREATERTHANOREQUAL',
                    'property' => 'dates.start',
                    'value' => new \DateTime,
                ]
            ];
        }

        $events = $this->eventRepository->findDemanded($demands, $limit, $orderby);

        $this->view->assign('events', $events);

        if (isset($this->settings['template']) && $this->settings['template']) {
            $this->view->setTemplatePathAndFilename($this->settings['template']);
        }

        $table = 'tx_qbevents_domain_model_event';
        isset($GLOBALS['TSFE']) && $GLOBALS['TSFE']->addCacheTags(array($table));
    }

    /**
     * @param  Event $event
     * @return void
     */
    public function showAction(Event $event)
    {
        $this->view->assign('event', $event);

        $table = 'tx_qbevents_domain_model_event';
        isset($GLOBALS['TSFE']) && $GLOBALS['TSFE']->addCacheTags(array($table . '_' . $event->getUid()));
    }

    /**
     * @param \TYPO3\CMS\Extbase\Mvc\RequestInterface $request
     * @param \TYPO3\CMS\Extbase\Mvc\ResponseInterface $response
     * @throws \Exception|\TYPO3\CMS\Extbase\Property\Exception
     */
    public function processRequest(\TYPO3\CMS\Extbase\Mvc\RequestInterface $request, \TYPO3\CMS\Extbase\Mvc\ResponseInterface $response)
    {
        try {
            parent::processRequest($request, $response);
        } catch (\TYPO3\CMS\Extbase\Property\Exception\InvalidSourceException $e) {
            $GLOBALS['TSFE']->pageNotFoundAndExit('Event not found');
        } catch (\TYPO3\CMS\Extbase\Property\Exception\TargetNotFoundException $e) {
            $GLOBALS['TSFE']->pageNotFoundAndExit('Event is no longer available', 'HTTP/1.0 410 Gone');
        }
        catch(\TYPO3\CMS\Extbase\Property\Exception $e) {
            $p = $e->getPrevious();

            if ($p instanceof  \TYPO3\CMS\Extbase\Property\Exception\InvalidSourceException && $p->getCode() === 1297931020) {
                $GLOBALS['TSFE']->pageNotFoundAndExit('Event not found.');
            } elseif ($p instanceof \TYPO3\CMS\Extbase\Property\Exception\TargetNotFoundException && $p->getCode() === 1297933823) {
                $GLOBALS['TSFE']->pageNotFoundAndExit('Event is no longer available', 'HTTP/1.0 410 Gone');
            } else {
                throw $e;
            }
        }
    }

}
