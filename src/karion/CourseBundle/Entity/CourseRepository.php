<?php

namespace karion\CourseBundle\Entity;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Query;
use Doctrine\ORM\QueryBuilder;
use Symfony\Component\Security\Acl\Permission\MaskBuilder;
use Doctrine\ORM\NoResultException;

class CourseRepository extends EntityRepository
{

  public function olnyActive()
  {
    $query = $this->createQueryBuilder('c')
      ->where('c.active = :active')
      ->setParameter('active', 1)
      ->getQuery();

    $courses = $query->getResult();

    return $courses;
  }

  /**
   * Return Course with Lessons
   * @param int $id
   * @return Course
   */
  public function getOneWithLesson($id)
  {
    $a = $this->createQueryBuilder('c')
      ->select('c, l')
      ->LeftJoin('c.lessons', 'l')
      ->where('c.id = :id')
      ->setParameter('id', $id)
      ->getQuery()
      ->getSingleResult();

    return $a;
  }

  /**
   * Get all User Course ID with ACL Mask
   * @param \karion\UserBundle\Entity\User $user
   * @param int $mask
   * @return array 
   */
  private function getIDWithAclMask(\karion\UserBundle\Entity\User $user, $mask)
  {
    $em = $this->getEntityManager();

    $query = 'SELECT DISTINCT o.object_identifier as id '
            .'FROM acl_object_identities as o '
            .'INNER JOIN acl_classes c ON c.id = o.class_id '
            .'LEFT JOIN acl_entries e ON e.class_id = o.class_id '
            .'LEFT JOIN acl_security_identities s ON s.id = e.security_identity_id '
            
            .'WHERE c.class_type = "'.$this->getEntityName().'" '
            .'AND s.identifier = "'.get_class($user) . '-' . $user->getUserName() .'" '
            .'AND e.mask & '. $mask 
      ;

    $conn = $em->getConnection();
    $all = $conn->fetchAll($query);
    
    $ids = array();
    foreach($all as $hit)
    {
      $ids[] = $hit["id"];
    }
    return $ids;
  }
  
  public function countAllWithAclEdit(\karion\UserBundle\Entity\User $user)
  {
    $mask = MaskBuilder::MASK_EDIT; // int(4)
    
    $ids = $this->getIDWithAclMask($user, $mask);
    
    return count($ids);
    
  }
  public function getAllWithAclEdit(\karion\UserBundle\Entity\User $user)
  {
    $mask = MaskBuilder::MASK_EDIT; // int(4)
    
    $ids = $this->getIDWithAclMask($user, $mask);
    
    //warunek na $ids
    $qb = $this->createQueryBuilder('c');
    $query = $qb
      ->add('where', $qb->expr()->in('c.id', $ids))
      ->getQuery();
    
    return $query->getResult();
  }

}