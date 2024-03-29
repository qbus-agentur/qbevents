<?php
namespace Qbus\Qbevents\Controller;

use Psr\Http\Message\ResponseInterface;
use TYPO3\CMS\Core\Pagination\SimplePagination;
use TYPO3\CMS\Extbase\Mvc\Controller\ActionController;
use TYPO3\CMS\Extbase\Mvc\RequestInterface;
use TYPO3\CMS\Extbase\Property\Exception\InvalidSourceException;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Frontend\Controller\ErrorController;
use TYPO3\CMS\Core\Http\ImmediateResponseException;
use TYPO3\CMS\Extbase\Property\Exception\TargetNotFoundException;
use TYPO3\CMS\Extbase\Pagination\QueryResultPaginator;
use Qbus\Qbevents\Domain\Model\Event;
use Qbus\Qbevents\Domain\Repository\EventRepository;


/**
 * EventController
 *
 * @author Benjamin Franzke <bfr@qbus.de>
 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License, version 3 or later
 */
class EventController extends ActionController
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

        $demands = [];
        if (isset($this->settings['demands']) && is_array($this->settings['demands']) && count($this->settings['demands']) > 0) {
            $demands = $this->settings['demands'];
        }
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

        $eventsPerPage = $this->settings['events_per_page'] ?? 0;
        if ($eventsPerPage) {
            $paginationClass = $this->settings['pagination_class'] ?? SimplePagination::class;
            $paginationClassArgs = $this->settings['pagination_class_args'] ?? [];

            $currentPage = $this->request->hasArgument('currentPage') ? (int)$this->request->getArgument('currentPage') : 1;
            $paginator = GeneralUtility::makeInstance(QueryResultPaginator::class, $events, $currentPage, $eventsPerPage);
            $pagination = GeneralUtility::makeInstance($paginationClass, $paginator, ...$paginationClassArgs);

            $this->view->assign('pagination', [
                'currentPage' => $currentPage,
                'paginator' => $paginator,
                'pagination' => $pagination,
            ]);
        }

        if (isset($this->settings['template']) && $this->settings['template']) {
            if (!method_exists($this->view, 'setTemplatePathAndFilename')) {
                throw new \Exception('View ' . get_class($this->view) . ' does not support template override', 1552556765);
            }
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
    public function processRequest(RequestInterface $request): ResponseInterface
    {
        try {
            return parent::processRequest($request);
        } catch (InvalidSourceException $e) {
            $response = GeneralUtility::makeInstance(ErrorController::class)->pageNotFoundAction($GLOBALS['TYPO3_REQUEST'], 'Event not found');
            throw new ImmediateResponseException($response);
        } catch (TargetNotFoundException $e) {
            $response = GeneralUtility::makeInstance(ErrorController::class)->pageNotFoundAction($GLOBALS['TYPO3_REQUEST'], 'Event is no longer available');
            throw new ImmediateResponseException($response);
        }
        catch(\TYPO3\CMS\Extbase\Property\Exception $e) {
            $p = $e->getPrevious();

            if ($p instanceof  InvalidSourceException && $p->getCode() === 1297931020) {
                $response = GeneralUtility::makeInstance(ErrorController::class)->pageNotFoundAction($GLOBALS['TYPO3_REQUEST'], 'Event not found.');
                throw new ImmediateResponseException($response);
            } elseif ($p instanceof TargetNotFoundException && $p->getCode() === 1297933823) {
                $response = GeneralUtility::makeInstance(ErrorController::class)->pageNotFoundAction($GLOBALS['TYPO3_REQUEST'], 'Event is no longer available');
                throw new ImmediateResponseException($response);
            } else {
                throw $e;
            }
        }
    }

}
