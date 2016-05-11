<?php
namespace Qbus\Qbevents\Domain\Repository;

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

    public function findUpcoming()
    {
        $query = $this->createQuery();

        $query->matching(
            $query->logicalAnd(
                $query->greaterThan('start', new \DateTime('NOW')),
                $query->equals('frequency', 0)
            )
        );

        return $query->execute();
    }
}
