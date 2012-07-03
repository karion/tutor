<?php
namespace karion\UserBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

use karion\UserBundle\Entity\User;
use karion\UserBundle\Entity\Group;
use karion\UserBundle\Entity\GroupRepository;

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
    
    $username = $input->getArgument('email');
    
    try
    {
      $user = $doctrine
                ->getRepository('karionUserBundle:User')
                ->findOneByUsername($username);
    }
    catch (\Exception $e)
    {
      $output->writeln('<error>Nie udało się odnaleźć użytkownika '.$username.'</error>');
      return 1; //bład powłoki
    }
    
    $GroupRepository = $doctrine->getRepository('karionUserBundle:Group');
    
    
    if($user->getGroups()->contains( $GroupRepository->getRole(GroupRepository::ROLE_ADMIN) ) )
    {
      $output->writeln('<error>Użytkownik '.$username.' jest już Adminem.</error>');
      return 1; //bład powłoki
    }

    $user->addGroup(
      $GroupRepository->getRole(GroupRepository::ROLE_ADMIN)
      );

    $em = $doctrine->getEntityManager();
    
    try
    {
      $em->persist($user);
      $em->flush();
    }
    catch(\Exception $e)
    {
      $output->writeln('<error> Zapis do bazy nie powiódł się</error>');
      return 1;//bład powłoki
    }
    
    $output->writeln('<info>Użytkownik '.$username.' został Adminem.</info>'  );
    return 0; //sukces
  }

}