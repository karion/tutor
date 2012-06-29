<?php

namespace karion\UserBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilder;

class UserType extends AbstractType
{

  public function buildForm(FormBuilder $builder, array $options)
  {
    $builder->add(
      'username', 'email', array('label' => "Email")
    );

    $builder->add('password', 'repeated', array(
      'type' => 'password',
      'invalid_message' => 'Hasło musi być identyczne.',
      'first_name' => 'Hasło',
      'second_name' => 'Powtórz hasło'
    ));

    $builder->add(
      'name', null, array(
      'label' => 'Imię i nazwisko',
      'trim' => true
      )
    );

    $builder->add(
      'statute', 'checkbox', array(
      'label' => "Akceptuję regulamin",
      'property_path' => false)
    );
  }

  public function getName()
  {
    return 'user';
  }

}