<?php
namespace Qbus\Qbevents\Domain\Repository;

use TYPO3\CMS\Extbase\Persistence\QueryInterface;

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


    public function findUpcoming($demands, $limit)
    {
        $query = $this->createQuery();

        $constraints = array(
            $query->greaterThan('start', new \DateTime('NOW')),
            $query->equals('frequency', 0),
        );

        $additional = $this->getConstraintsForDemand($query, $demands);
        if ($additional !== null) {
            $constraints[] = $additional;
        }

        $query->matching($query->logicalAnd($constraints));

        if ($limit) {
            $query->setLimit((int) $limit);
        }

        return $query->execute();
    }

    /**
     * Get a QueryInterface constraint from an array definition
     *
     * @param QueryInterface $query
     * @param array          $demands
     * @param string         $conjunction
     *
     * @return void
     */
    protected function getConstraintsForDemand($query, $demands, $conjunction = 'AND')
    {
        $constraints = array();

        if (!is_array($demands) || empty($demands)) {
            return null;
        }

        foreach ($demands as $key => $demand) {
            if (!isset($demand['demand']))
                continue;
            $constraint = $demand['demand'];

            switch ($constraint['operation']) {
            case 'EQUALS':
                $constraints[] = $query->equals($constraint['property'], $constraint['value']);
                break;
            case 'LIKE':
                $constraints[] = $query->like($constraint['property'], $constraint['value']);
                break;
            case 'CONTAINS':
                $constraints[] = $query->contains($constraint['property'], $constraint['value']);
                break;
            case 'LESSTHAN':
                $constraints[] = $query->lessThan($constraint['property'], $constraint['value']);
                break;
            case 'LESSTHANOREQUAL':
                $constraints[] = $query->lessThanOrEqual($constraint['property'], $constraint['value']);
                break;
            case 'GREATERTHAN':
                $constraints[] = $query->greaterThan($constraint['property'], $constraint['value']);
                break;
            case 'GREATERTHANOREQUAL':
                $constraints[] = $query->greaterThanOrEqual($constraint['property'], $constraint['value']);
                break;
            case 'AND':
                $constraints[] = $this->getConstraintsForDemand($query, $constraint['operands'], 'AND');
                break;
            case 'OR':
                $constraints[] = $this->getConstraintsForDemand($query, $constraint['operands'], 'OR');
                break;
            default:
                return null;
            }
        }

        $result = null;
        switch ($conjunction)  {
        case 'AND':
            $result = $query->logicalAnd($constraints);
            break;
        case 'OR':
            $result = $query->logicalOr($constraints);
            break;
        }

        return $result;
    }
}
