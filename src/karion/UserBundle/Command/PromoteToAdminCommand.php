<?php
namespace karion\UserBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

use karion\UserBundle\Entity\User;
use karion\UserBundle\Entity\Group;
//use karion\UserBundle\Entity\GroupRepository;

class PromoteToAdminCommand extends ContainerAwareCommand
{

  protected function configure()
  {
    $this
      ->setName('karion:promoteToAdmin')
      ->setDescription('Add Admin_role to user')
      ->addArgument(
        'email', 
        InputArgument::REQUIRED, 
        'Email użytkownika wyznaczonego na Admina'
        )
    ;
  }

  //@todo dodać sprawdzanie aktualnych uprawnień.
  protected function execute(InputInterface $input, OutputInterface $output)
  {
    $container = $this->getContainer();
    $doctrine = $container->get('doctrine');
    $repository = $doctrine->getRepository('karionUserBundle:User');
    $username = $input->getArgument('email');
    try
    {
      $user = $repository->findOneByUsername($username);
    }
    catch (\Exception $e)
    {
      $output->writeln('Niepowodzenie!');
      return 0;
    }
    
    $GroupRepository = $doctrine->getRepository('karionUserBundle:Group');
    
    /* @var karion\UserBundle\Entity\User $user  */
    $user->addGroup(
      $GroupRepository->getRole(\karion\UserBundle\Entity\GroupRepository::ROLE_ADMIN)
      );

    $em = $doctrine->getEntityManager();
    
    try
    {
      $em->persist($user);
      $em->flush();
    }
    catch(\Exception $e)
    {
      $output->writeln('niepowodzenie');
      return 0;
    }
    
    $output->writeln('Użytkownik '.$username.' został Adminem.'  );
  }

}