<?php
namespace Qbus\Qbevents\Domain\Repository;

use Qbus\Qbevents\Utility\DemandsUtility;

/**
 * EventDateRepository
 *
 * @author Benjamin Franzke <bfr@qbus.de>
 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License, version 3 or later
 */
class EventDateRepository extends \TYPO3\CMS\Extbase\Persistence\Repository
{
    /**
     * @var array
     */
    protected $defaultOrderings = [
        'start' => \TYPO3\CMS\Extbase\Persistence\QueryInterface::ORDER_ASCENDING,
    ];

    /**
     * @param int        $uid
     * @param array|NULL $enableFieldsToBeIgnored
     *
     * @return void
     */
    public function findRecurrencesByUid($uid, $enableFieldsToBeIgnored = null)
    {
        $query = $this->createQuery();
        $query->getQuerySettings()->setRespectStoragePage(false);

        if ($enableFieldsToBeIgnored) {
            $query->getQuerySettings()->setIgnoreEnableFields(true);
            $query->getQuerySettings()->setEnableFieldsToBeIgnored($enableFieldsToBeIgnored);
        }

        $query->matching(
            $query->equals('baseDate', $uid)
        );

        return $query->execute();
    }

    public function findUpcoming($demands = array(), $limit = 0, $returnRawQueryResult = false)
    {
        $query = $this->createQuery();

        /* Our EventDates are not localizable. Only the Event's are. So lets ignore the sys language flag.
         * This is actually needed so that extbase does not create sql queries that try to find direct
         * relations betweens translated Events and EventDates (as the relation between EventDate and Event
         * can only be established in the default language) */
        $query->getQuerySettings()->setRespectSysLanguage(false);

        $constraints = array(
            $query->greaterThan('start', new \DateTime('NOW')),
            $query->equals('frequency', 0),
        );

        $additional = DemandsUtility::getConstraintsForDemand($query, $demands);
        if ($additional !== null) {
            $constraints[] = $additional;
        }

        $query->matching($query->logicalAnd($constraints));

        if ($limit) {
            $query->setLimit((int) $limit);
        }

        return $query->execute($returnRawQueryResult);
    }

    /**
     * Returns bookings in range
     *
     * @return QueryResultInterface
     */
    public function findInRange($begin, $end)
    {
        $query = $this->createQuery();

        $query->matching(
            $query->logicalOr(
                /* There are four different cases for range "positioning":
                 * with $begin and $end being the search delimiters
                 *
                 * $begin  von     bis   $end
                 * von     $begin  bis   $end
                 * $begin  von     $end  bis
                 * von     $begin  $end  bis
                 *
                 * where rule 1 is a subset of (2 || 3)
                 */
                /*
                $query->logicalAnd(
                        $query->greaterThanOrEqual('start', $begin),
                        $query->lessThanOrEqual('start', $end),
                        $query->greaterThanOrEqual('end', $begin),
                        $query->lessThanOrEqual('end', $end)
                ),*/
                $query->logicalAnd(
                    $query->greaterThanOrEqual('start', $begin),
                    $query->lessThanOrEqual('start', $end)
                ),
                $query->logicalAnd(
                    $query->greaterThanOrEqual('end', $begin),
                    $query->lessThanOrEqual('end', $end)
                ),
                $query->logicalAnd(
                    $query->lessThan('start', $begin),
                    $query->greaterThan('end', $end)
                )
            )
        );

        return $query->execute();
    }
}
