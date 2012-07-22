<?php

namespace karion\CourseBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * karion\CourseBundle\Entity\Course
 */
class Course
{
    /**
     * @var integer $id
     */
    private $id;

    /**
     * @var string $title
     */
    private $title;

    /**
     * @var text $description
     */
    private $description;

    /**
     * @var boolean $active
     */
    private $active = false;


    /**
     * Get id
     *
     * @return integer 
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set title
     *
     * @param string $title
     */
    public function setTitle($title)
    {
        $this->title = $title;
    }

    /**
     * Get title
     *
     * @return string 
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * Set description
     *
     * @param text $description
     */
    public function setDescription($description)
    {
        $this->description = $description;
    }

    /**
     * Get description
     *
     * @return text 
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Set active
     *
     * @param boolean $active
     */
    public function setActive($active)
    {
        $this->active = $active;
    }

    /**
     * Get active
     *
     * @return boolean 
     */
    public function getActive()
    {
        return $this->active;
    }
    /**
     * @var karion\LessonBundle\Entity\Lesson
     */
    private $lessons;

    public function __construct()
    {
        $this->lesssons = new \Doctrine\Common\Collections\ArrayCollection();
        $this->active = false;
    }
    
    /**
     * Add lesssons
     *
     * @param karion\LessonBundle\Entity\Lesson $lesssons
     */
    public function addLesson(\karion\LessonBundle\Entity\Lesson $lessons)
    {
        $this->lessons[] = $lessons;
        $lessons->setCourse($this);
    }

    /**
     * Get lessons
     *
     * @return Doctrine\Common\Collections\Collection 
     */
    public function getLessons()
    {
        return $this->lessons;
    }
}