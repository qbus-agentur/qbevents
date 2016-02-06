<?php
namespace Qbus\Qbevents\Domain\Model;

/**
 * EventDate
 */
class EventDate extends \TYPO3\CMS\Extbase\DomainObject\AbstractEntity
{
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
     * type
     *
     * @var int
     */
    protected $type = 0;

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
     * Returns the type
     *
     * @return int $type
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Sets the type
     *
     * @param  int $type
     * @return void
     */
    public function setType($type)
    {
        $this->type = $type;
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
     * @param  int $baseDate
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
}
