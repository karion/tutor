<?php

namespace karion\CourseBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilder;

class CourseType extends AbstractType
{

  public function buildForm(FormBuilder $builder, array $options)
  {
    $builder->add(
      'title', 'text', array('label' => "Tytuł")
    );

    $builder->add(
      'description', 'textarea', array(
        'label' => "Opis kursu",
        'attr' => array(
          'class' => 'tinymce',
          'data-theme' => 'medium' // simple, advanced, bbcode
        )
      )
    );
  }

  public function getName()
  {
    return 'course';
  }

}