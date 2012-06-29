<?php

namespace karion\UserBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * karion\UserBundle\Entity\UserToken
 */
class UserToken
{

  /**
   * @var integer $id
   */
  private $id;

  /**
   * @var string $token
   */
  private $token;

  /**
   * @var integer $tokenType
   */
  private $tokenType;

  /**
   * @var datetime $activeTo
   */
  private $activeTo;

  /**
   * @var karion\UserBundle\Entity\User
   */
  private $user;

  //type constants:
  const TYPE_ACTIVATION_TOKEN = 1;
  const TYPE_FORGOTEN_PASSWORD_TOKEN = 2;
  
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
   * Set token
   *
   * @param string $token
   */
  public function setToken($token)
  {
    $this->token = $token;
  }

  /**
   * Get token
   *
   * @return string 
   */
  public function getToken()
  {
    return $this->token;
  }

  /**
   * Set tokenType
   *
   * @param integer $tokenType
   */
  public function setTokenType($tokenType)
  {
    $this->tokenType = $tokenType;
  }

  /**
   * Get tokenType
   *
   * @return integer 
   */
  public function getTokenType()
  {
    return $this->tokenType;
  }

  /**
   * Set activeTo
   *
   * @param datetime $activeTo
   */
  public function setActiveTo($activeTo)
  {
    $this->activeTo = $activeTo;
  }

  /**
   * Get activeTo
   *
   * @return datetime 
   */
  public function getActiveTo()
  {
    return $this->activeTo;
  }

  /**
   * Set user
   *
   * @param karion\UserBundle\Entity\User $user
   */
  public function setUser(\karion\UserBundle\Entity\User $user)
  {
    $this->user = $user;
  }

  /**
   * Get user
   *
   * @return karion\UserBundle\Entity\User 
   */
  public function getUser()
  {
    return $this->user;
  }
  
  public function generateToken()
  {
    $this->setToken(hash('sha256', (uniqid(null, true))));
  }
  
  /**
   *
   * @param integer $days
   * @return unixTime 
   */
  public static function nowPlusDays($days)
  {
    $days = (int)$days;
    if($days > 0)
    {
      return date( 'Y-m-d H:i:s',  time() + $days*(24*60*60)  );
    }
    else
    {
      return date( 'Y-m-d H:i:s',time());
    }
  }

}