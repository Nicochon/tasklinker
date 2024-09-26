<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use App\Entity\Project;
use App\Entity\Users;
use App\Entity\ProjectUser;

class UpdateProjectType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $project = $options['project'];
        $usersProject = $options['usersProject'];
        $builder
            ->add('name', TextType::class, [
                'label' => 'Titre du projet',
                'required' => true,
                'attr' => ['maxlength' => 255],
                'data' => $project ? $project->getName() : '',
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
                    'data-disabled' => json_encode(array_map(fn($user) => $user->getId(), $usersProject)),
                ],
            ])
            ->add('continuer', SubmitType::class, [
                'label' => 'Continuer',
                'attr' => ['class' => 'button button-submit'],
            ]);
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => null,
            'users' => [],
            'usersProject' => [],
            'project' => [],
        ]);
    }
}
