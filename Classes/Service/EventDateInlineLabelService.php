<?php
namespace Qbus\Qbevents\Service;

use TYPO3\CMS\Core\Utility\MathUtility;

/**
 * EventDateInlineLabelService
 *
 * @author Benjamin Franzke <bfr@qbus.de>
 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License, version 3 or later
 */
class EventDateInlineLabelService
{
    /**
     * @param  array $params
     * @return void
     */
    public function getInlineLabel(array &$params)
    {
        if (!isset($params['row']['start']) || !MathUtility::canBeInterpretedAsInteger($params['row']['start']) || $params['row']['start'] == 0) {
            return;
        }

        $dateFormats = $this->getDateFormats();
        $format = 'datetime';
        if (isset($params['row']['is_full_day']) && $params['row']['is_full_day']) {
            $format = 'date';
        }

        $params['title'] = strftime($dateFormats[$format], $params['row']['start']);

        if (isset($params['row']['end']) && MathUtility::canBeInterpretedAsInteger($params['row']['end']) && $params['row']['end'] > 0) {
            $params['title'] .= ' – ' . strftime($dateFormats[$format], $params['row']['end']);
        }
    }

    /**
     * @return array
     */
    protected function getDateFormats()
    {
        // set all date times available
        $dateFormats = array(
            'date' => '%d-%m-%Y',
            'year' => '%Y',
            'time' => '%H:%M',
            'timesec' => '%H:%M:%S'
        );
        if ($GLOBALS['TYPO3_CONF_VARS']['SYS']['USdateFormat']) {
            $dateFormats['date'] = '%m-%d-%Y';
        }
        $dateFormats['datetime'] = $dateFormats['date'] . ' ' . $dateFormats['time'];
        $dateFormats['datetimesec'] = $dateFormats['date'] . ' ' . $dateFormats['timesec'];

        return $dateFormats;
    }
}
