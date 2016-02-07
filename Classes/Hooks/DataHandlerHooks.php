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

        if ($status === 'update' && isset($fields['hidden']) && $fields['hidden']) {
            $this->getEventRecurrenceService()->hideRecurrences($id);
            return;
        }

        if ($status === 'update' && isset($fields['hidden']) && !$fields['hidden']) {
            $this->getEventRecurrenceService()->unhideRecurrences($id);
        }

        $change = (
            $status === 'new' ||
            $status === 'update' && (
                isset($fields['hidden']) ||
                isset($fields['type']) ||
                isset($fields['start']) ||
                isset($fields['end']) ||
                isset($fields['is_full_day']) ||
                isset($fields['frequency']) ||
                isset($fields['frequency_count']) ||
                isset($fields['frequency_until'])
            )
        );

        if ($change) {
            if ($status === 'new') {
                $id = $dataHandler->substNEWwithIDs[$id];
            }
            $this->getEventRecurrenceService()->updateRecurrences($id);
        }
    }

    /*
     * @param string      $table
     * @param int         $id
     * @param bool        $recordWasDeleted
     * @param DataHandler $dataHandler
     */
    public function processCmdmap_deleteAction(
        $table,
        $id,
        array $record,
        &$recordWasDeleted,
        DataHandler $dataHandler
    ) {
        if ($table !== self::EVENTDATE_TABLE) {
            return;
        }

        $this->getEventRecurrenceService()->removeRecurrences($id);
    }

    /**
     * @return \Qbus\Qbevents\Service\EventRecurrenceService
     */
    protected function getEventRecurrenceService()
    {
        return $this->getObjectManager()->get(\Qbus\Qbevents\Service\EventRecurrenceService::class);
    }

    /**
     * @return \TYPO3\CMS\Extbase\Object\ObjectManager
     */
    protected function getObjectManager()
    {
        return \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance(\TYPO3\CMS\Extbase\Object\ObjectManager::class);
    }
}
