<?php

namespace karion\CourseBundle\Entity;
 
use Doctrine\ORM\EntityRepository;
 
class CourseRepository extends EntityRepository
{
  public function olnyActive()
	{
    
//    $repository->createQueryBuilder('p')
//    ->where('p.price > :price')
//    ->setParameter('price', '19.99')
//    ->orderBy('p.price', 'ASC')
//    ->getQuery();
    $query = $this->createQueryBuilder('c')
      ->where('c.active = :active')
      ->setParameter('active', 1)
      ->getQuery();
    
    $courses = $query->getResult();

    return $courses;
	}
  
  public function getOneWithLesson($id)
  {
    $a = $this->createQueryBuilder('c')
        ->select('c, l')
        ->innerJoin('c.lessons', 'l')
        ->where('c.id = :id')
        ->setParameter('id', $id )
        ->getQuery()
      
        ->getSingleResult();
    
    
    return $a;
  }
}