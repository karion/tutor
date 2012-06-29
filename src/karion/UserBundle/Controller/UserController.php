<?php

namespace karion\UserBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

use Symfony\Component\Security\Core\SecurityContext;

use Doctrine\ORM\NoResultException;
use karion\UserBundle\Entity\User;
use karion\UserBundle\Entity\UserToken;

use karion\UserBundle\Form\Type\UserType;



class UserController extends Controller
{
  public function newAction()
  {
    $user = new User();
    
    $form = $this->createForm(new UserType(),$user);
    
    return $this->render('karionUserBundle:User:new.html.twig', array(
        'form' => $form->createView()
      ));
  }
  
  public function createAction( Request $request)
  {
    $user = new User();

    $form = $this->createForm(new UserType(),$user);

    $form->bindRequest($request);

    if ($form->isValid()) {
      // zapisz do bazy user
      $factory = $this->container->get('security.encoder_factory');
      $encoder = $factory->getEncoder($user);
      $user->setPassword(
        $encoder->encodePassword(
          $user->getPassword(), 
          $user->getSalt()
          ));
      
      $em = $this->getDoctrine()->getEntityManager();
      $em->persist($user);
      $em->flush();
      
      // zapisz do bazy user
      $token = new UserToken();
      $token->setUser($user);
      $token->setTokenType(UserToken::TYPE_ACTIVATION_TOKEN);
      $token->generateToken();
      $token->setActiveTo(
        new \DateTime( 
          UserToken::nowPlusDays(
            $this->container->getParameter('user_token_activation_time') 
        )),'text/html');
      $em->persist($token);
      $em->flush();
      
      //wyślij maila aktywacyjnego
      $message = \Swift_Message::newInstance()
        ->setSubject('Tutor: aktywuj konto')
        ->setFrom('karion@o2.pl')
        ->setTo( $user->getUsername() )
        ->setBody(
          $this->renderView(
            'karionUserBundle:User:activationMail.html.twig', 
            array(
              'user' => $user,
              'token' => $token
              )),'text/html'
          )
      ;
      $this->get('mailer')->send($message);
      
      // przekierowanie
      return $this->redirect($this->generateUrl('userCreateInfo'));
    }
    
    //wyświetlenie formularza z błędami
    return $this->render('karionUserBundle:User:new.html.twig', array(
        'form' => $form->createView()
      ));
    
  }
  public function createInfoAction(){
    return $this->render('karionUserBundle:User:createInfo.html.twig');
  }
  
  public function activateAction(Request $request){
    $token = $request->query->get('token',null);
    $error = null;
    
    if($token)
    {
      try
      {
        // @var $userToken UserToken 
        $userToken = $this->getDoctrine()
				->getRepository('karionUserBundle:UserToken')
        ->findOneActiveByToken($token); 
       
        $userToken->getUser()->setIsActive(true);
        
        $em = $this->getDoctrine()->getEntityManager();
        $em->remove($userToken);
        $em->flush();
        
      }
      catch (NoResultException  $exc)
      {
        $error = "Aktywacja nie powiodłą się. Błędny token.";
      }
      
    }
    else
    {
      $error = 'Aktywacja nie powiodłą się. Brak tokena';
    }
    
    return $this->render(
      'karionUserBundle:User:activate.html.twig',
      array('error' => $error )
      );
  }
  
  public function editAction(){}
  public function editPostAction(){}
  
}