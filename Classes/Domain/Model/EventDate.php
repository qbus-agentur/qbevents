<?php
namespace Qbus\Qbevents\Domain\Model;

/**
 * EventDate
 */
class EventDate extends \TYPO3\CMS\Extbase\DomainObject\AbstractEntity
{
    /**
     * hidden
     *
     * @var bool
     */
    protected $hidden = 0;

    /**
     * start
     *
     * @var DateTime
     */
    protected $start = null;

    /**
     * end
     *
     * @var DateTime
     */
    protected $end = null;

    /**
     * isFullDay
     *
     * @var bool
     */
    protected $isFullDay = false;

    /**
     * baseDate
     *
     * @var int
     */
    protected $baseDate = 0;

    /**
     * event
     *
     * @var \Qbus\Qbevents\Domain\Model\Event
     */
    protected $event = null;

    /**
     * frequency
     *
     * @var int
     */
    protected $frequency = '';

    /**
     * frequencyCount
     *
     * @var int
     */
    protected $frequencyCount = 0;

    /**
     * frequencyUntil
     *
     * @var DateTime
     */
    protected $frequencyUntil = null;

    /**
     * Gets hidden
     *
     * @return bool
     */
    public function getHidden()
    {
        return $this->hidden;
    }

    /**
     * Sets hidden
     *
     * @param  bool $hidden
     * @return void
     */
    public function setHidden($hidden)
    {
        $this->hidden = $hidden;
    }

    /**
     * Returns the start
     *
     * @return DateTime $start
     */
    public function getStart()
    {
        return $this->start;
    }

    /**
     * Sets the start
     *
     * @param  DateTime $start
     * @return void
     */
    public function setStart($start)
    {
        $this->start = $start;
    }

    /**
     * Returns the end
     *
     * @return DateTime $end
     */
    public function getEnd()
    {
        return $this->end;
    }

    /**
     * Sets the end
     *
     * @param  DateTime $end
     * @return void
     */
    public function setEnd($end)
    {
        $this->end = $end;
    }

    /**
     * Returns the isFullDay
     *
     * @return bool $isFullDay
     */
    public function getIsFullDay()
    {
        return $this->isFullDay;
    }

    /**
     * Sets the isFullDay
     *
     * @param  bool $isFullDay
     * @return void
     */
    public function setIsFullDay($isFullDay)
    {
        $this->isFullDay = $isFullDay;
    }

    /**
     * Returns the baseDate
     *
     * @return int $baseDate
     */
    public function getBaseDate()
    {
        return $this->baseDate;
    }

    /**
     * Sets the baseDate
     *
     * @param  int  $baseDate
     * @return void
     */
    public function setBaseDate($baseDate)
    {
        $this->baseDate = $baseDate;
    }

    /**
     * Sets the Event
     *
     * @return \Qbus\Qbevents\Domain\Model\Event
     */
    public function getEvent()
    {
        return $this->event;
    }

    /**
     * Gets the Event
     *
     * @var \Qbus\Qbevents\Domain\Model\Event $event
     */
    public function setEvent($event)
    {
        $this->event = $event;
    }

    /**
     * Returns the frequency
     *
     * @return int $frequency
     */
    public function getFrequency()
    {
        return $this->frequency;
    }

    /**
     * Sets the frequency
     *
     * @param  int  $frequency
     * @return void
     */
    public function setFrequency($frequency)
    {
        $this->frequency = $frequency;
    }

    /**
     * Returns the frequencyCount
     *
     * @return int $frequencyCount
     */
    public function getFrequencyCount()
    {
        return $this->frequencyCount;
    }

    /**
     * Sets the frequencyCount
     *
     * @param  int  $frequencyCount
     * @return void
     */
    public function setFrequencyCount($frequencyCount)
    {
        $this->frequencyCount = $frequencyCount;
    }

    /**
     * Returns the frequencyUntil
     *
     * @return DateTime $frequencyUntil
     */
    public function getFrequencyUntil()
    {
        return $this->frequencyUntil;
    }

    /**
     * Sets the frequencyUntil
     *
     * @param  DateTime $frequencyUntil
     * @return void
     */
    public function setFrequencyUntil($frequencyUntil)
    {
        $this->frequencyUntil = $frequencyUntil;
    }
}
