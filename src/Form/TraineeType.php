<?php

namespace App\Form;

use App\Entity\Session;
use App\Entity\Trainee;
use App\Form\FormNewTraineeType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

class TraineeType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('firstName')
            ->add('lastName')
            ->add('dateOfBirth', null, [
                'widget' => 'single_text',
            ])
            ->add('email')
            ->add('address')
            ->add('postcode')
            ->add('city')
            ->add('sessions', EntityType::class, [
                'class' => Session::class,
                'choice_label' => 'name',
                'multiple' => true,
                // 'expandable' => false
            ])
            ->add('submit', SubmitType::class, [
                'attr'=> [
                    'class' => 'submit-btn'
                ]
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Trainee::class,
        ]);
    }
}
