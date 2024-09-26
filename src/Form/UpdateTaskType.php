<?php

namespace App\Form;

use App\Entity\Task;
use App\Entity\Users;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class UpdateTaskType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $task = $options['task'];
        $usersTask = $options['usersTask'];

        $builder
            ->add('name', TextType::class, [
                'label' => 'Titre de la tâche',
                'required' => true,
                'attr' => ['maxlength' => 255],
                'data' => $task ? $task->getName() : '',
            ])
            ->add('description', TextareaType::class, [
                'label' => 'Description',
                'required' => false,
                'data' => $task ? $task->getDescription() : '',
            ])
            ->add('endDate', DateType::class, [
                'label' => 'Date',
                'widget' => 'single_text',
                'required' => true,
                'data' => $task ? $task->getEndDate() : '',
            ])
            ->add('status', ChoiceType::class, [
                'label' => 'Statut',
                'choices' => [
                    'To Do' => 'To Do',
                    'Doing' => 'Doing',
                    'Done' => 'Done',
                ],
                'required' => true,
                'data' => $task ? $task->getStatus() : '',
            ])
            ->add('employes', ChoiceType::class, [
                'label' => 'Inviter des membres',
                'choices' => $options['users'],
                'choice_label' => function(Users $user) {
                    return $user->getFirstName() . ' ' . $user->getLastName();
                },
                'choice_value' => function(?Users $user) {
                    return $user?->getId();
                },
                'multiple' => true,
                'expanded' => false,
                'attr' => [
                    'class' => 'custom-select',
                    'data-disabled' => json_encode(array_map(fn($user) => $user->getId(), $usersTask)),
                ],
            ])
            ->add('continuer', SubmitType::class, [
                'label' => 'Continuer',
                'attr' => ['class' => 'button button-submit'],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => null,
            'users' => [],
            'usersTask' => [],
            'task' => [],
        ]);
    }
}
