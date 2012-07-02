<?php

namespace karion\UserBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\AdvancedUserInterface;


use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
/**
 * karion\UserBundle\Entity\User
 */
class User implements AdvancedUserInterface
{
    /**
     * @var integer $id
     */
    private $id;

    /**
     * @var string $username
     */
    private $username;

    /**
     * @var string $salt
     */
    private $salt;

    /**
     * @var string $password
     */
    private $password;

    /**
     * @var boolean $isActive
     */
    private $isActive;

    /**
     * @var string $name
     */
    private $name;
    
    /**
     * @var karion\UserBundle\Entity\UserToken
     */
    private $tokens;
    
    /**
     * @var karion\UserBundle\Entity\Group
     */
    private $groups;
    


    public function __construct()
    {
      $this->isActive = false;
      $this->salt = hash('sha256', (uniqid(null, true)));
      $this->groups = new ArrayCollection();
    }
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
     * Set username
     *
     * @param string $username
     */
    public function setUsername($username)
    {
        $this->username = $username;
    }

    /**
     * Get username
     *
     * @return string 
     */
    public function getUsername()
    {
        return $this->username;
    }

    /**
     * Set salt
     *
     * @param string $salt
     */
    public function setSalt($salt)
    {
        $this->salt = $salt;
    }

    /**
     * Get salt
     *
     * @return string 
     */
    public function getSalt()
    {
        return $this->salt;
    }

    /**
     * Set password
     *
     * @param string $password
     */
    public function setPassword($password)
    {
        $this->password = $password;
    }

    /**
     * Get password
     *
     * @return string 
     */
    public function getPassword()
    {
        return $this->password;
    }

    /**
     * Set isActive
     *
     * @param boolean $isActive
     */
    public function setIsActive($isActive)
    {
        $this->isActive = $isActive;
    }

    /**
     * Get isActive
     *
     * @return boolean 
     */
    public function getIsActive()
    {
        return $this->isActive;
    }

    /**
     * Set name
     *
     * @param string $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * Get name
     *
     * @return string 
     */
    public function getName()
    {
        return $this->name;
    }
     
    public function eraseCredentials()
    {
    }
    
    public function equals(UserInterface $user)
    {
        return $this->id === $user->getId();
    }
    
    public function isAccountNonExpired()
    {
        return true;
    }

    public function isAccountNonLocked()
    {
        return true;
    }

    public function isCredentialsNonExpired()
    {
        return true;
    }

    public function isEnabled()
    {
        return $this->isActive;
    }

    /**
     * Add tokens
     *
     * @param karion\UserBundle\Entity\UserToken $lessons
     */
    public function addUserToken(\karion\UserBundle\Entity\UserToken $tokens)
    {
        $this->tokens[] = $tokens;
    }

    /**
     * Get lessons
     *
     * @return Doctrine\Common\Collections\Collection 
     */
    public function getTokens()
    {
        return $this->tokens;
    }

    /**
     * Add groups
     *
     * @param karion\UserBundle\Entity\Group $groups
     */
    public function addGroup(\karion\UserBundle\Entity\Group $groups)
    {
        $this->groups[] = $groups;
    }

    /**
     * Get groups
     *
     * @return Doctrine\Common\Collections\Collection 
     */
    public function getGroups()
    {
        return $this->groups;
    }
    
    public function getRoles()
    {
        return $this->groups->toArray();
    }
}