<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use App\Entity\Users;

class AddTaskType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class, [
                'label' => 'Titre de la tâche',
                'required' => true,
                'attr' => ['maxlength' => 255],
            ])
            ->add('description', TextareaType::class, [
                'label' => 'Description',
                'required' => true,
                'attr' => ['maxlength' => 255],
            ])
            ->add('date', DateType::class, [
                'label' => 'Date de début du projet',
                'widget' => 'single_text',
                'required' => true,
            ])
            ->add('status', ChoiceType::class, [
                'label' => 'Statut de la tâche',
                'choices' => [
                    'À faire' => 'To Do',
                    'En cours' => 'Doing',
                    'Terminé' => 'Done',
                ],
                'expanded' => false,
                'multiple' => false,
                'required' => true,
            ])
            ->add('employes', EntityType::class, [
                'class' => Users::class,
                'label' => 'Inviter des membres',
                'choice_label' => function(Users $user) {
                    return $user->getFirstName() . ' ' . $user->getLastName();
                },
                'multiple' => true,
                'expanded' => false,
            ])
            ->add('submit', SubmitType::class, [
                'label' => 'Ajouter',
                'attr' => ['class' => 'button button-submit'],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => null,
            'users' => [],
        ]);
    }
}
