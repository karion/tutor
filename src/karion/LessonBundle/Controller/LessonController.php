<?php

namespace karion\LessonBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;


class LessonController extends Controller
{
    
    public function showAction($id)
    {
      $lesson = $this->getDoctrine()
			->getRepository('karionLessonBundle:Lesson')
      //->find($id);
      ->getLessonWithCourse($id);
      
      return $this->render(
              'karionLessonBundle:Lesson:show.html.twig', 
              array(
                  'lesson' => $lesson
                  )
              );
    }
}
