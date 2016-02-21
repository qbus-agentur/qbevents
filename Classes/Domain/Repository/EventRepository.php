<?php
namespace Qbus\Qbevents\Domain\Repository;

use Qbus\Qbevents\Utility\DemandsUtility;

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
     * @param $demands
     */
    public function findDemanded($demands, $limit)
    {
        $query = $this->createQuery();

        $constraints = DemandsUtility::getConstraintsForDemand($query, $demands);

        if ($limit) {
            $query->setLimit((int) $limit);
        }
        $query->matching($constraints);

        return $query->execute();
    }

}
