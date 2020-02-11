<?php
namespace Qbus\Qbevents\Domain\Repository;

use Qbus\Qbevents\Utility\DemandsUtility;
use TYPO3\CMS\Extbase\Persistence\QueryInterface;
use TYPO3\CMS\Extbase\Persistence\QueryResultInterface;


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
     * @return QueryResultInterface
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

        $result = $query->execute();
        if (!$result instanceof QueryResultInterface) {
            throw new \RuntimeException(
                QueryInterface::class . '::execute(false) didn\'t return an instance of ' . QueryResultInterface::class,
                1552572230
            );
        }

        return $result;
    }

    /**
     * @param array $demands
     * @param int $limit
     * @param bool $returnRawQueryResult
     * @return QueryResultInterface|array
     */
    public function find($demands = array(), $limit = 0, $returnRawQueryResult = false)
    {
        $query = $this->createQuery();

        /* Our EventDates are not localizable. Only the Event's are. So lets ignore the sys language flag.
         * This is actually needed so that extbase does not create sql queries that try to find direct
         * relations betweens translated Events and EventDates (as the relation between EventDate and Event
         * can only be established in the default language) */
        $query->getQuerySettings()->setRespectSysLanguage(false);

        $constraints = array(
            $query->equals('frequency', 0),
            $query->equals('event.hidden', 0),
            $query->equals('event.deleted', 0),
        );

        $additional = DemandsUtility::getConstraintsForDemand($query, $demands);
        if ($additional !== null) {
            $constraints[] = $additional;
        }

        if (!empty($constraints)) {
            $query->matching($query->logicalAnd($constraints));
        }

        if ($limit) {
            $query->setLimit((int) $limit);
        }

        return $query->execute($returnRawQueryResult);
    }


    /**
     * @param array $demands
     * @param int $limit
     * @param bool $returnRawQueryResult
     * @return QueryResultInterface|array
     */
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
            $query->equals('event.hidden', 0),
            $query->equals('event.deleted', 0),
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
     * @param \DateTime $begin
     * @param \DateTime $end
     * @return QueryResultInterface
     */
    public function findInRange($begin, $end)
    {
        $query = $this->createQuery();

        $query->matching(
            $query->logicalOr([
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
                $query->logicalAnd([
                    $query->greaterThanOrEqual('start', $begin),
                    $query->lessThanOrEqual('start', $end)
                ]),
                $query->logicalAnd([
                    $query->greaterThanOrEqual('end', $begin),
                    $query->lessThanOrEqual('end', $end)
                ]),
                $query->logicalAnd([
                    $query->lessThan('start', $begin),
                    $query->greaterThan('end', $end)
                ])
            ])
        );

        $result = $query->execute();
        if (!$result instanceof QueryResultInterface) {
            throw new \RuntimeException(
                QueryInterface::class . '::execute(false) didn\'t return an instance of ' . QueryResultInterface::class,
                1552574257
            );
        }

        return $result;
    }
}
