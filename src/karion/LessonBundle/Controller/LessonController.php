<?php

namespace karion\LessonBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;


class LessonController extends Controller
{
    
    public function showAction($id)
    {
      $lesson = $this->getDoctrine()
			->getRepository('karionLessonBundle:Lesson')
      ->getLessonWithCourse($id);
      
      return $this->render(
              'karionLessonBundle:Lesson:show.html.twig', 
              array(
                  'lesson' => $lesson
                  )
              );
    }
    
    public function showBySlugAction($courseId,$slug)
    {
      $lesson = $this->getDoctrine()
			->getRepository('karionLessonBundle:Lesson')
      ->getLessonWithCourseBySlug($courseId, $slug);
      
      if(is_null($lesson))
      {
        throw( new \Symfony\Component\HttpKernel\Exception\NotFoundHttpException("Nie odnaleziono lekcji"));
      }
      return $this->render(
              'karionLessonBundle:Lesson:show.html.twig', 
              array(
                  'lesson' => $lesson
                  )
              );
    }

}
