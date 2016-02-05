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
}
