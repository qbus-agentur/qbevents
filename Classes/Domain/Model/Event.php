<?php
namespace Qbus\Qbevents\Domain\Model;

/**
 * Event
 */
class Event extends \TYPO3\CMS\Extbase\DomainObject\AbstractEntity
{
    /**
     * title
     *
     * @var string
     */
    protected $title = '';

    /**
     * location
     *
     * @var string
     */
    protected $location = '';

    /**
     * teaser
     *
     * @var string
     */
    protected $teaser = '';

    /**
     * description
     *
     * @var string
     */
    protected $description = '';

    /**
     * dates
     *
     * @var \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\Qbus\Qbevents\Domain\Model\EventDate>
     * @lazy
     * @cascade remove
     */
    protected $dates = null;

    /**
     * image
     *
     * @var \TYPO3\CMS\Extbase\Domain\Model\FileReference
     */
    protected $image = null;

    /**
     * categories
     *
     * @var \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\TYPO3\CMS\Extbase\Domain\Model\Category>
     * @lazy
     */
    protected $categories = null;

    /**
     * __construct
     */
    public function __construct()
    {
        $this->dates = new \TYPO3\CMS\Extbase\Persistence\ObjectStorage();
        $this->categories = new \TYPO3\CMS\Extbase\Persistence\ObjectStorage();
    }

    /**
     * Returns the title
     *
     * @return string $title
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * Sets the title
     *
     * @param  string $title
     * @return void
     */
    public function setTitle($title)
    {
        $this->title = $title;
    }

    /**
     * Returns the location
     *
     * @return string $location
     */
    public function getLocation()
    {
        return $this->location;
    }

    /**
     * Sets the location
     *
     * @param  string $location
     * @return void
     */
    public function setLocation($location)
    {
        $this->location = $location;
    }

    /**
     * Returns the teaser
     *
     * @return string $teaser
     */
    public function getTeaser()
    {
        return $this->teaser;
    }

    /**
     * Sets the teaser
     *
     * @param  string $teaser
     * @return void
     */
    public function setTeaser($teaser)
    {
        $this->teaser = $teaser;
    }

    /**
     * Returns the description
     *
     * @return string $description
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Sets the description
     *
     * @param  string $description
     * @return void
     */
    public function setDescription($description)
    {
        $this->description = $description;
    }

    /**
     * Adds a Date
     *
     * @param  EventDate $date
     * @return void
     */
    public function addDate(EventDate $date)
    {
        $this->dates->attach($date);
    }

    /**
     * Removes a Date
     *
     * @param  EventDate $dateToRemove The Date to be removed
     * @return void
     */
    public function removeDate(EventDate $dateToRemove)
    {
        $this->dates->detach($dateToRemove);
    }

    /**
     * Returns the dates
     *
     * @return \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\Qbus\Qbevents\Domain\Model\EventDate> $dates
     */
    public function getDates()
    {
        return $this->dates;
    }

    /**
     * Sets the dates
     *
     * @param  \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\Qbus\Qbevents\Domain\Model\EventDate> $dates
     * @return void
     */
    public function setDates($dates)
    {
        $this->dates = $dates;
    }

    /**
     * Returns the image
     *
     * @return \TYPO3\CMS\Extbase\Domain\Model\FileReference $image
     */
    public function getImage()
    {
        return $this->image;
    }

    /**
     * Sets the image
     *
     * @param  \TYPO3\CMS\Extbase\Domain\Model\FileReference $image
     * @return void
     */
    public function setImage($image)
    {
        $this->image = $image;
    }

    /**
     * Returns the categories
     *
     * @return \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\TYPO3\CMS\Extbase\Domain\Model\Category> $categories
     */
    public function getCategories()
    {
        return $this->categories;
    }

    /**
     * Sets the categories
     *
     * @param  \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\TYPO3\CMS\Extbase\Domain\Model\Category> $categories
     * @return void
     */
    public function setCategories($categories)
    {
        $this->categories = $categories;
    }
}
