<?php
namespace Qbus\Qbevents\Hooks;

use Psr\Container\ContainerInterface;
use Qbus\Qbevents\Service\EventRecurrenceService;
use TYPO3\CMS\Core\DataHandling\DataHandler;

/**
 * DataHandlerHooks
 *
 * @author Benjamin Franzke <bfr@qbus.de>
 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License, version 3 or later
 */
class DataHandlerHooks
{
    const EVENTDATE_TABLE = 'tx_qbevents_domain_model_eventdate';

    private ContainerInterface $container;

    /**
     * Array that holds deferred id's deferred to be processed after all operations
     *
     * @var array
     */
    private $deferred = array();

    public function __construct(
        ContainerInterface $container
    ) {
        $this->container = $container;
    }

    /**
     * @param  DataHandler $dataHandler
     * @return void
     */
    public function processDatamap_afterAllOperations(/*DataHandler $dataHandler*/)
    {
        if (count($this->deferred) > 0) {
            foreach ($this->deferred as $uid) {
                $this->getEventRecurrenceService()->updateRecurrences($uid);
            }
        }
        $this->deferred = [];
    }

    /**
     * Hook executed after the DataHandler performed one database operation
     *
     * @param  string      $status
     * @param  string      $table
     * @param  int|string  $id
     * @param  array       $fields
     * @param  DataHandler $dataHandler
     * @return void
     */
    public function processDatamap_afterDatabaseOperations(
        $status,
        $table,
        $id,
        &$fields,
        DataHandler $dataHandler
    ) {
        if ($table !== self::EVENTDATE_TABLE) {
            return;
        }

        /* Defer new records to the "afterAllOperations" state, since the
           "event" pointer is not filled in "afterDatabaseOperations" state.
           Background: the event pointer is by the RelationHandler during
           the DataHandlers remapStack phase */
        if ($status === 'new') {
            $id = $dataHandler->substNEWwithIDs[$id];
            $this->deferred[] = $id;
            return;

        }

        if ($status === 'update' && isset($fields['hidden']) && $fields['hidden']) {
            $this->getEventRecurrenceService()->hideRecurrences((int)$id);

            return;
        }

        if ($status === 'update' && isset($fields['hidden']) && !$fields['hidden']) {
            $this->getEventRecurrenceService()->unhideRecurrences((int)$id);
        }

        $change = (
            $status === 'update' && (
                isset($fields['hidden']) ||
                isset($fields['type']) ||
                isset($fields['start']) ||
                isset($fields['end']) ||
                isset($fields['is_full_day']) ||
                isset($fields['frequency']) ||
                isset($fields['frequency_count']) ||
                isset($fields['frequency_until']) ||
                isset($fields['frequency_weekdays'])
            )
        );

        if ($change) {
            $this->getEventRecurrenceService()->updateRecurrences((int)$id);
        }
    }

    protected function getEventRecurrenceService(): EventRecurrenceService
    {
        return $this->container->get(EventRecurrenceService::class);
    }
}
