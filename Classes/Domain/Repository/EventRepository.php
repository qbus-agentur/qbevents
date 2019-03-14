<?php
namespace Qbus\Qbevents\Domain\Repository;

use Qbus\Qbevents\Utility\DemandsUtility;
use TYPO3\CMS\Extbase\Persistence\QueryResultInterface;

/**
 * EventRepository
 *
 * @author Benjamin Franzke <bfr@qbus.de>
 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License, version 3 or later
 */
class EventRepository extends \TYPO3\CMS\Extbase\Persistence\Repository
{
    /**
     * findDemanded
     *
     * @param array $demands
     * @param int|string $limit
     * @param string $orderBy
     * @return QueryResultInterface
     */
    public function findDemanded($demands, $limit, $orderBy = null)
    {
        $query = $this->createQuery();

        if ($limit) {
            $query->setLimit((int) $limit);
        }
        $constraints = DemandsUtility::getConstraintsForDemand($query, $demands);
        if ($constraints !== null) {
            $query->matching($constraints);
        }

        if ($orderBy) {
            $query->setOrderings([
                $orderBy =>  \TYPO3\CMS\Extbase\Persistence\QueryInterface::ORDER_ASCENDING,
            ]);
        }

        return $query->execute();
    }
}
