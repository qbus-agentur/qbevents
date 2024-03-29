<?php
namespace Qbus\Qbevents\Domain\Model;

use TYPO3\CMS\Extbase\Annotation as Extbase;
use TYPO3\CMS\Extbase\DomainObject\AbstractEntity;
use TYPO3\CMS\Extbase\Persistence\ObjectStorage;
use TYPO3\CMS\Extbase\Domain\Model\FileReference;
use TYPO3\CMS\Extbase\Annotation\ORM\Cascade;
use TYPO3\CMS\Extbase\Annotation\ORM\Lazy;

/**
 * Event
 */
class Event extends AbstractEntity
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
     * @Lazy
     * @Cascade("remove")
     * @Extbase\ORM\Lazy
     * @Extbase\ORM\Cascade("remove")
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
     * @var \TYPO3\CMS\Extbase\Persistence\ObjectStorage<Category>
     * @Lazy
     * @Extbase\ORM\Lazy
     */
    protected $categories = null;

    /**
     * files
     *
     * @var \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\TYPO3\CMS\Extbase\Domain\Model\FileReference>
     * @Lazy
     * @Extbase\ORM\Lazy
     */
    protected $files = null;

    /**
     * externalUrl
     *
     * @var string
     */
    protected $externalUrl = '';

    /**
     * __construct
     */
    public function __construct()
    {
        $this->dates = new ObjectStorage();
        $this->categories = new ObjectStorage();
        $this->files = new ObjectStorage();
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
     * Get a list of upcoming dates
     * @TODO: Resolve recurrences
     *
     * @return array
     */
    public function getUpcomingDates()
    {
        $upcoming = [];
        $now = new \DateTime;
        foreach ($this->dates as $date) {
            if ($date->getStart() >= $now) {
                $upcoming[] = $date;
            }
        }

        return $upcoming;
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
     * @return \TYPO3\CMS\Extbase\Persistence\ObjectStorage<Category> $categories
     */
    public function getCategories()
    {
        return $this->categories;
    }

    /**
     * Sets the categories
     *
     * @param  \TYPO3\CMS\Extbase\Persistence\ObjectStorage<Category> $categories
     * @return void
     */
    public function setCategories($categories)
    {
        $this->categories = $categories;
    }

    /**
     * Returns the files
     *
     * @return \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\TYPO3\CMS\Extbase\Domain\Model\FileReference> $files
     */
    public function getFiles()
    {
        return $this->files;
    }

    /**
     * Sets the files
     *
     * @param  \TYPO3\CMS\Extbase\Persistence\ObjectStorage $files
     * @return void
     */
    public function setFiles($files)
    {
        $this->files = $files;
    }

    /**
     * Adds a file to this files.
     *
     * @param  \TYPO3\CMS\Extbase\Domain\Model\FileReference $file
     * @return void
     */
    public function addFile(FileReference $file)
    {
        if ($this->getFiles() === null) {
            $this->files = new ObjectStorage();
        }
        $this->getFiles()->attach($file);
    }

    /**
     * Returns the externalUrl
     *
     * @return string $externalUrl
     */
    public function getExternalUrl()
    {
        return $this->externalUrl;
    }

    /**
     * Sets the externalUrl
     *
     * @param  string $externalUrl
     * @return void
     */
    public function setExternalUrl($externalUrl)
    {
        $this->externalUrl = $externalUrl;
    }

    /*
     * @return int
     */
    public function getLanguageUid()
    {
        return $this->_languageUid;
    }
}
