<?php
namespace Qbus\Qbevents\Hooks;

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

    /**
     * Array that holds deferred id's deferred to be processed after all operations
     *
     * @var array
     */
    static protected $deferred = array();

    /**
     * @param  DataHandler $dataHandler
     * @return void
     */
    public function processDatamap_afterAllOperations(DataHandler $dataHandler)
    {
        if (count(self::$deferred) > 0) {
            foreach (self::$deferred as $uid) {
                $this->getEventRecurrenceService()->updateRecurrences($uid);
            }
        }
        self:$deferred = array();
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
            self::$deferred[] = $id;
            return;

        }

        if ($status === 'update' && isset($fields['hidden']) && $fields['hidden']) {
            $this->getEventRecurrenceService()->hideRecurrences($id);

            return;
        }

        if ($status === 'update' && isset($fields['hidden']) && !$fields['hidden']) {
            $this->getEventRecurrenceService()->unhideRecurrences($id);
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
            $this->getEventRecurrenceService()->updateRecurrences($id);
        }
    }

    /**
     * @return \Qbus\Qbevents\Service\EventRecurrenceService
     */
    protected function getEventRecurrenceService()
    {
        return $this->getExtbaseObjectManager()->get(\Qbus\Qbevents\Service\EventRecurrenceService::class);
    }

    /**
     * @return \TYPO3\CMS\Extbase\Object\ObjectManager
     */
    protected function getExtbaseObjectManager()
    {
        return \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance(\TYPO3\CMS\Extbase\Object\ObjectManager::class);
    }
}
