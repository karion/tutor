<?php

namespace karion\CourseBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
//use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\Security\Acl\Domain\ObjectIdentity;
use Symfony\Component\Security\Acl\Domain\UserSecurityIdentity;
use Symfony\Component\Security\Acl\Permission\MaskBuilder;
//use karion\CourseBundle\Entity;
use karion\CourseBundle\Entity\Course;
use karion\CourseBundle\Form\Type\CourseType;
use karion\UserBundle\Entity\GroupRepository;

class AdminController extends Controller
{

  public function createAction(Request $request)
  {
    if(false === $this->get('security.context')
        ->isGranted(GroupRepository::ROLE_CREATOR))
    {
      throw new AccessDeniedException();
    }

    $course = new Course();

    $form = $this->createForm(new CourseType(), $course);

    if($request->getMethod() == 'POST')
    {
      $form->bindRequest($request);

      if($form->isValid())
      {
        try
        {
          $em = $this->getDoctrine()->getEntityManager();
          $em->persist($course);
          $em->flush();
        }
        catch(\Exception $e)
        {
          //@todo 
          return $this->render(
              'karionCourseBundle:Admin:create.html.twig', array(
                'form'  => $form->createView(),
                'error' => 'zapis do bazy się niepowiódł, spróbuj ponownie'
            ));
        }

        // creating the ACL
        $aclProvider    = $this->get('security.acl.provider');
        $objectIdentity = ObjectIdentity::fromDomainObject($course);
        $acl            = $aclProvider->createAcl($objectIdentity);

        // retrieving the security identity of the currently logged-in user
        $securityContext  = $this->get('security.context');
        $user             = $securityContext->getToken()->getUser();
        $securityIdentity = UserSecurityIdentity::fromAccount($user);

        // grant owner access
        $mask = new MaskBuilder();
        $mask
          ->add(MaskBuilder::MASK_OWNER)
          ->add(MaskBuilder::MASK_EDIT)
          ->add(MaskBuilder::MASK_DELETE);
        $acl->insertObjectAce($securityIdentity, $mask->get());
        $aclProvider->updateAcl($acl);


        return $this->redirect(
            $this->generateUrl(
              'karionCourseBundleAdmin_edit', array('id' => $course->getId())
            )
        );
      }
    }
    return $this->render('karionCourseBundle:Admin:create.html.twig', array(
          'form' => $form->createView()
      ));
  }

  public function editAction(Request $request, $id)
  {
    $securityContext = $this->get('security.context');
    $course          = $this->getDoctrine()
      ->getRepository('karionCourseBundle:Course')
      ->getOneWithLesson($id);

    // check for edit access
    if(false === $securityContext->isGranted('EDIT', $course))
    {
      throw new AccessDeniedException();
    }

    $form = $this->createForm(new CourseType(), $course);
    $enableForm = $this->createEnableForm($course);

    
    if($request->getMethod() == 'POST')
    {
      $form->bindRequest($request);

      if($form->isValid())
      {
        try
        {
          $em = $this->getDoctrine()->getEntityManager();
          $em->persist($course);
          $em->flush();
        }
        catch(\Exception $e)
        {
          return $this->render(
              'karionCourseBundle:Admin:edit.html.twig', array(
                'form'  => $form->createView(),
                'enableForm' => $enableForm->createView(),
                'type' => $course->getActiveType(),
                'error' => 'zapis do bazy się nie powiódł, spróbuj ponownie'
            ));
        }

        return $this->redirect(
            $this->generateUrl(
              'karionCourseBundleAdmin_edit', array('id' => $course->getId())
            )
        );
      }
    }

    return $this->render('karionCourseBundle:Admin:edit.html.twig', array(
          'form'   => $form->createView(),
          'enableForm' => $enableForm->createView(),
          'type' => $course->getActiveType(),
          'course' => $course
      ));
  }

  public function listAction()
  {
    if(false === $this->get('security.context')
        ->isGranted(GroupRepository::ROLE_CREATOR))
    {
      throw new AccessDeniedException();
    }

    $courses = $this->getDoctrine()
      ->getRepository('karionCourseBundle:Course')
      ->getAllWithAclEdit(
      $this->get('security.context')->getToken()->getUser()
    );


    return $this->render('karionCourseBundle:Admin:list.html.twig', array(
          'courses' => $courses
      ));
  }

  public function enableAction(Request $request, $id)
  {
    /* @var $course Course */
    $securityContext = $this->get('security.context');
    $course          = $this->getDoctrine()
      ->getRepository('karionCourseBundle:Course')
      ->getOneWithLesson($id);

    // check for edit access
    if(false === $securityContext->isGranted('EDIT', $course))
    {
      throw new AccessDeniedException();
    }

    $form = $this->createEnableForm($course);

    $form->bindRequest($request);

      if($form->isValid())
      {
        $data = $form->getData();
        try
        {
          if($data['type'] != $course->getActiveType())
          {
            throw new \Exception('Zły typ.') ;
          }

          if($data['id'] != $course->getId())
          {
            throw new \Exception('Błedne id') ;
          }

          if($data['type'] == Course::ENABLE_TYPE_NOT_ENABLE )
          {
            throw new \Exception('Nie można aktywować kursu bez lekcji!') ;
          }

          //aktywacja
          switch($data['type'])
          {
            case Course::ENABLE_TYPE_ENABLE:
              $course->setActive(true);
              $message = 'Kurs został opublikowany.';
              break;
            case Course::ENABLE_TYPE_DISABLE:
              $course->setActive(false);
              $message = 'Cofnięto publikację kursu.';
              break;
            case Course::ENABLE_TYPE_NOT_ENABLE:
            default:
              throw new \Exception('Nieznany błąd');
          }
          
          $em = $this->getDoctrine()->getEntityManager();
          $em->persist($course);
          $em->flush();
          $this->get('session')->setFlash('info', $message );
          
        }
        catch(\Exception $e)
        {
          $this->get('session')->setFlash('error', $e->getMessage() );
        }
      }
      else
      {
        $this->get('session')->setFlash('error', 'Nie można wykonać tej operacji!' );
      }
      
      return $this->redirect(
            $this->generateUrl(
              'karionCourseBundleAdmin_edit', array('id' => $course->getId())
            )
        );
  }

  
  /**
   * Create form for CSRF protectet buttons
   * @param type $id
   * @return type
   */
  protected function createEnableForm(Course $course)
  {
    return $this
      ->createFormBuilder(array('id' => $course->getId(), 'type' => $course->getActiveType() ))
      ->add('id', 'hidden')
      ->add('type', 'hidden')
      ->getForm()
    ;
  }

}