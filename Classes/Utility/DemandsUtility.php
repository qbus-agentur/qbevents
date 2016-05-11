<?php
namespace Qbus\Qbevents\Utility;

use TYPO3\CMS\Extbase\Persistence\QueryInterface;
use TYPO3\CMS\Extbase\Persistence\Generic\Qom\ConstraintInterface;


/**
 * DemandsUtil
 *
 * @author Benjamin Franzke <bfr@qbus.de>
 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License, version 3 or later
 */
class DemandsUtility
{
    /**
     * Get a QueryInterface constraint from an array definition
     *
     * @param QueryInterface $query
     * @param array          $demands
     * @param string         $conjunction
     *
     * @return ConstraintInterface|null
     */
    public static function getConstraintsForDemand($query, $demands, $conjunction = 'AND')
    {
        $constraints = array();

        if (!is_array($demands) || empty($demands)) {
            return null;
        }

        foreach ($demands as $key => $demand) {
            if (!isset($demand['demand'])) {
                continue;
            }
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
                $tmp = self::getConstraintsForDemand($query, $constraint['operands'], 'AND');
                if ($tmp !== null) {
                    $constraints[] = $tmp;
                }
                break;
            case 'OR':
                $tmp = self::getConstraintsForDemand($query, $constraint['operands'], 'OR');
                if ($tmp !== null) {
                    $constraints[] = $tmp;
                }
                break;
            default:
                return null;
            }
        }

        if (count($constraints) == 0) {
            return null;
        }

        $result = null;

        switch ($conjunction) {
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
