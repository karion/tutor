<?php

namespace karion\CourseBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

use Symfony\Component\Validator\Mapping\ClassMetadata;
use Symfony\Component\Validator\Constraints\Collection;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\MinLength;
use Symfony\Component\Validator\Constraints\MaxLength;

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

    const ENABLE_TYPE_DISABLE = 1;
    const ENABLE_TYPE_ENABLE = 0;
    const ENABLE_TYPE_NOT_ENABLE = -1;

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
     * Set id
     *
     * @param integer $id
     */
    public function setId($id)
    {
        $this->id = $id;
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
      if($active == true && count($this->lessons) == 0 )
      {
        $this->active = false;
        throw new \Exception('Nie można aktywować kursu bez lekcji!') ;
      }
      else
      {
        $this->active = $active;
      }
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
    
    /**
     * Get Active Type
     * Active Type say if Course can be enabled
     * or disabled.
     * Course can be enabled olny if has lessons
     */
    public function getActiveType()
    {
      if($this->getActive() == true)
      {
        return self::ENABLE_TYPE_DISABLE;
      }
      elseif($this->getActive() == false && $this->getLessons()->count() == 0)
      {
        return self::ENABLE_TYPE_NOT_ENABLE;
      }
      else
      {
        return self::ENABLE_TYPE_ENABLE;
      }
    }
    
    public static function loadValidatorMetadata(ClassMetadata $metadata)
    {
        $metadata->addPropertyConstraint('title', new NotBlank());
        $metadata->addPropertyConstraint(
          'title', 
          new MinLength(
            array(
                'limit'       => 5, 
                'message'     => 'Tytuł musi byś dłuższy niż {{ limit }} znaków'
                )
            )
          );
      
        $metadata->addPropertyConstraint(
          'description', 
          new MinLength(
            array(
                'limit'   => 25, 
                'message' => 'Opis kursu musi byś dłuższy niż {{ limit }} znaków')
            )
          );
        
    }
}