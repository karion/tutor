<?php

namespace karion\CourseBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
//use Symfony\Component\HttpFoundation\Response;

use karion\CourseBundle\Entity;
use karion\CourseBundle\Entity\Course;

class CourseController extends Controller
{

  public function listAction()
  {
    $courses = $this->getDoctrine()
			->getRepository('karionCourseBundle:Course')
      ->olnyActive();

    return $this->render(
            'karionCourseBundle:Course:list.html.twig', 
            array(
                'courses' => $courses
                )
            );
  }
  
  public function showAction($id)
  {
    $course = $this->getDoctrine()
			->getRepository('karionCourseBundle:Course')
      ->getOneWithLesson($id);
//->find($id);
    $pseudotrue = true;
    return $this->render(
            'karionCourseBundle:Course:show.html.twig', 
            array(
                'course' => $course,
                )
            );
  }


  
}
