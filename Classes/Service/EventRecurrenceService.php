<?php
namespace Qbus\Qbevents\Service;

use TYPO3\CMS\Extbase\Persistence\Generic\PersistenceManager;
use Recurr\Rule;
use Recurr\Transformer\ArrayTransformer;
use Recurr\Transformer\ArrayTransformerConfig;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use Qbus\Qbevents\Domain\Model\EventDate;
use Qbus\Qbevents\Domain\Repository\EventDateRepository;
use Recurr\RecurrenceCollection;
use TYPO3\CMS\Core\SingletonInterface;

/**
 * EventRecurrenceService
 *
 * @author Benjamin Franzke <bfr@qbus.de>
 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License, version 3 or later
 */
class EventRecurrenceService implements SingletonInterface
{
    public static $freqs = array(
        1 => 'YEARLY',
        2 => 'MONTHLY',
        3 => 'WEEKLY',
        4 => 'DAILY',
    );

    /**
     * @var EventDateRepository
     */
    protected $eventDateRepository;

    /**
     * @var \TYPO3\CMS\Extbase\Persistence\Generic\PersistenceManager
     */
    protected $persistenceManager;

    /**
     * @var \Qbus\Qbevents\Service\ConfigurationService
     */
    protected $configurationService;

    /**
     * @param  EventDateRepository $eventDateRepository
     * @return void
     */
    public function injectEventDateRepository(EventDateRepository $eventDateRepository)
    {
        $this->eventDateRepository = $eventDateRepository;
    }

    /**
     * @param  \TYPO3\CMS\Extbase\Persistence\Generic\PersistenceManager $persistenceManager
     * @return void
     */
    public function injectPersistenceManager(PersistenceManager $persistenceManager)
    {
        $this->persistenceManager = $persistenceManager;
    }

    /**
     * @param  \Qbus\Qbevents\Service\ConfigurationService $configurationService
     * @return void
     */
    public function injectConfigurationService(ConfigurationService $configurationService)
    {
        $this->configurationService = $configurationService;
    }

    /**
     * @param int $eventDateUid
     */
    public function hideRecurrences($eventDateUid)
    {
        $dates = $this->eventDateRepository->findRecurrencesByUid($eventDateUid);
        foreach ($dates as $date) {
            $date->setHidden(true);
            $this->eventDateRepository->update($date);
        }
        $this->persistenceManager->persistAll();
    }

    /**
     * @param int $eventDateUid
     */
    public function unhideRecurrences($eventDateUid)
    {
        $dates = $this->eventDateRepository->findRecurrencesByUid($eventDateUid, ['disabled']);
        foreach ($dates as $date) {
            $date->setHidden(false);
            $this->eventDateRepository->update($date);
        }
        $this->persistenceManager->persistAll();
    }

    public function removeRecurrences($eventDateUid)
    {
        $dates = $this->eventDateRepository->findRecurrencesByUid($eventDateUid);
        foreach ($dates as $date) {
            $this->eventDateRepository->remove($date);
        }
        $this->persistenceManager->persistAll();
    }

    /**
     * @param int $eventDateUid
     */
    public function updateRecurrences($eventDateUid)
    {
        // @todo: Fix repository to deliver hidden relations (like a hidden event relation)
        $date = $this->eventDateRepository->findByUid($eventDateUid);
        if (!$date) {
            /* Do not update hidden records – they'll be updated when the are activated again */
            return;
        }
        if ($date->getFrequency() === 0) {
            /* Remove recurrences that may have existed before switching to frequency=0 */
            $this->removeRecurrences($eventDateUid);
            return;
        }

        if ($date->getStart()) {
            $startDate = $date->getStart();
            // Timezone is set to '+01:00' by default (with a European default timzeone)
            // set the timezone explictly to make DateTransitions work
            $startDate->setTimezone(new \DateTimeZone(date_default_timezone_get()));

            $endDate = null;
            if ($date->getEnd()) {
                $endDate = $date->getEnd();
                $endDate->setTimezone(new \DateTimeZone(date_default_timezone_get()));
            }

            if (!in_array($date->getFrequency(), array_keys(self::$freqs))) {
                return;
            }

            $rrule = [
                'FREQ' => self::$freqs[$date->getFrequency()],
            ];

            if ($date->getFrequencyUntil()) {
                $rrule['UNTIL'] = $date->getFrequencyUntil()->format(\DateTime::ATOM);
            } else {
                $rrule['COUNT'] = $date->getFrequencyCount();
            }

            if ($rrule['FREQ'] == 'WEEKLY') {
                $byday = $this->buildByDay($date->getFrequencyWeekdays());
                if ($byday) {
                    $rrule['BYDAY'] = $byday;
                }
            }

            $rule = new Rule($rrule, $startDate, $endDate);
            $transformer = new ArrayTransformer();

            $transformerConfig = new ArrayTransformerConfig();
            $limit = $this->configurationService->get('recurrence_virtual_limit');
            if ($limit) {
                $transformerConfig->setVirtualLimit($limit);
            }
            $transformerConfig->enableLastDayOfMonthFix();
            $transformer->setConfig($transformerConfig);

            $this->mergeUpdatesWithExistingRecurrences($date, $transformer->transform($rule));
        }
    }

    /**
     * @param EventDate            $date
     * @param RecurrenceCollection $recurrences
     */
    protected function mergeUpdatesWithExistingRecurrences(EventDate $date, RecurrenceCollection $recurrences)
    {
        $oldDates = $this->eventDateRepository->findRecurrencesByUid($date->getUid())->toArray();

        foreach ($recurrences as $recurrence) {
            $updated = false;

            $start = $recurrence->getStart();
            $end = $recurrence->getEnd();
            if ($start == $end) {
                $end = null;
            }

            foreach ($oldDates as $eKey => $old) {
                if ($old->getStart() == $start && $old->getEnd() == $end) {
                    $old->setIsFullDay($date->getIsFullDay());
                    $this->eventDateRepository->update($old);

                    unset($oldDates[$eKey]);
                    $updated = true;
                    break;
                }
            }
            if (!$updated) {
                $new = $this->cloneDateAsRecurrence($date, $start, $end);
                $this->eventDateRepository->add($new);
            }
        }

        foreach ($oldDates as $old) {
            $this->eventDateRepository->remove($old);
        }
        $this->persistenceManager->persistAll();
    }

    /**
     * @param EventDate $date
     * @param \DateTime $start
     * @param \DateTime $end
     */
    protected function cloneDateAsRecurrence(
        EventDate $date,
        \DateTime $start,
        \DateTime $end = null
    ) {
        $new = $this->cloneDate($date);
        $new->setBaseDate($date->getUid());

        $new->setStart($start);
        $new->setEnd($end);
        $new->setFrequency(0);

        return $new;
    }

    /**
     * @param  EventDate $date
     * @return EventDate
     */
    protected function cloneDate(EventDate $date)
    {
        /* @var $subDate EventDate */
        $subDate = GeneralUtility::makeInstance(EventDate::class);

        $subDate->setPid($date->getPid());
        $subDate->setFrequency($date->getFrequency());

        $subDate->setStart($date->getStart());
        $subDate->setEnd($date->getEnd());
        $subDate->setIsFullDay($date->getIsFullDay());

        $subDate->setEvent($date->getEvent());

        return $subDate;
    }

    /**
     * Build a BYDAY string
     *
     * e.g. 'MO,TU,FR,SU'
     *
     * @param  int
     * @return string
     */
    protected function buildByDay($weekdays)
    {
        $days = [];

        if ($weekdays == 0x7F) {
            // Return empty string, if all days are set.
            // No need for recurr to parse all those days
            return '';
        }

        if (($weekdays & 0x01) == 0x01) {
            $days[] = 'MO';
        }
        if (($weekdays & 0x02) == 0x02) {
            $days[] = 'TU';
        }
        if (($weekdays & 0x04) == 0x04) {
            $days[] = 'WE';
        }
        if (($weekdays & 0x08) == 0x08) {
            $days[] = 'TH';
        }
        if (($weekdays & 0x10) == 0x10) {
            $days[] = 'FR';
        }
        if (($weekdays & 0x20) == 0x20) {
            $days[] = 'SA';
        }
        if (($weekdays & 0x40) == 0x40) {
            $days[] = 'SU';
        }

        return implode(',', $days);
    }
}
