<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use App\Entity\Project;
use App\Entity\Users;

class AddProjectType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class, [
                'label' => 'Titre du projet',
                'required' => true,
                'attr' => ['maxlength' => 255],
            ])
            ->add('employes', ChoiceType::class, [
                'label' => 'Inviter des membres',
                'choices' => $options['users'],
                'choice_label' => function(Users $users) {
                    return $users->getFirstName() . ' ' . $users->getLastName();
                },
                'choice_value' => function(?Users $user) {
                    return $user?->getId();
                },

                'multiple' => true,
                'expanded' => false,
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
        ]);
    }
}
