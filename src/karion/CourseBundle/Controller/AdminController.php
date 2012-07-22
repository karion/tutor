<?php

namespace karion\CourseBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\Security\Acl\Domain\ObjectIdentity;
use Symfony\Component\Security\Acl\Domain\UserSecurityIdentity;
use Symfony\Component\Security\Acl\Permission\MaskBuilder;

use karion\CourseBundle\Entity;
use karion\CourseBundle\Entity\Course;
use karion\CourseBundle\Form\Type\CourseType;
use karion\UserBundle\Entity\GroupRepository;

class AdminController extends Controller
{

  public function createAction(Request $request)
  {
    if (false === $this->get('security.context')
                  ->isGranted(GroupRepository::ROLE_CREATOR))
    {
      throw new AccessDeniedException();
    }

    $course = new Course();

    $form = $this->createForm(new CourseType(), $course);

    if ($request->getMethod() == 'POST')
    {
      $form->bindRequest($request);

      if ($form->isValid())
      {
        try
        {
          $em = $this->getDoctrine()->getEntityManager();
          $em->persist($course);
          $em->flush();
        }
        catch (\Exception $e)
        {
          //@todo 
          return $this->render(
            'karionCourseBundle:Admin:create.html.twig', 
            array(
              'form' => $form->createView(),
              'error' => 'zapis do bazy się niepowiódł, spróbuj ponownie'
            ));
        }

        // creating the ACL
        $aclProvider = $this->get('security.acl.provider');
        $objectIdentity = ObjectIdentity::fromDomainObject($course);
        $acl = $aclProvider->createAcl($objectIdentity);

        // retrieving the security identity of the currently logged-in user
        $securityContext = $this->get('security.context');
        $user = $securityContext->getToken()->getUser();
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
              'karionCourseBundleAdmin_edit', 
              array('id' => $course->getId())
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
    $course = $this->getDoctrine()
			->getRepository('karionCourseBundle:Course')
      ->getOneWithLesson($id);
      
    // check for edit access
    if (false === $securityContext->isGranted('EDIT', $course))
    {
        throw new AccessDeniedException();
    }
    
    $form = $this->createForm(new CourseType(), $course);
    
    
    return $this->render('karionCourseBundle:Admin:create.html.twig', array(
        'form' => $form->createView()
      ));
  }
  
  public function listAction()
  {
    if (false === $this->get('security.context')
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

}