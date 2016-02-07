<?php
namespace Qbus\Qbevents\Service;

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
    public function injectPersistenceManager(\TYPO3\CMS\Extbase\Persistence\Generic\PersistenceManager $persistenceManager)
    {
        $this->persistenceManager = $persistenceManager;
    }

    /**
     * @param  \Qbus\Qbevents\Service\ConfigurationService $configurationService
     * @return void
     */
    public function injectConfigurationService(\Qbus\Qbevents\Service\ConfigurationService $configurationService)
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

    /**
     * @param int $eventDateUid
     */
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
        $date = $this->eventDateRepository->findByUid($eventDateUid);
        if (!$date) {
            /* Do not update hidden records – they'll be updated when the are activated again */
            return;
        }
        if ($date->getFrequency() === 0) {
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

            $rule = new \Recurr\Rule($rrule, $startDate, $endDate);
            $transformer = new \Recurr\Transformer\ArrayTransformer();

            $transformerConfig = new \Recurr\Transformer\ArrayTransformerConfig();
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
        $subDate = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance(EventDate::class);

        $subDate->setPid($date->getPid());
        $subDate->setFrequency($date->getFrequency());

        $subDate->setStart($date->getStart());
        $subDate->setEnd($date->getEnd());
        $subDate->setIsFullDay($date->getIsFullDay());

        $subDate->setEvent($date->getEvent());

        return $subDate;
    }
}
