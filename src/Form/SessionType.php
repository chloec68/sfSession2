<?php

namespace App\Form;

use App\Entity\User;
use App\Entity\Session;
use App\Entity\Trainee;
use App\Entity\Training;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

class SessionType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name')
            ->add('startingDate', null, [
                'widget' => 'single_text',
            ])
            ->add('endingDate', null, [
                'widget' => 'single_text',
            ])
            ->add('nbPlaces')
            ->add('trainees', EntityType::class, [
                'class' => Trainee::class,
                'choice_label' => 'id',
                'multiple' => true,
            ])
            ->add('training', EntityType::class, [
                'class' => Training::class,
                'choice_label' => 'id',
            ])
            ->add('staffMember', EntityType::class, [
                'class' => User::class,
                'choice_label' => 'id',
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
            'data_class' => Session::class,
        ]);
    }
}
