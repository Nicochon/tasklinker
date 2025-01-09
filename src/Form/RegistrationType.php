<?php

namespace App\Form;

use App\Entity\Users;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;

class RegistrationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('lastName', TextType::class, [
                'label' => 'Nom',
            ])
            ->add('firstName', TextType::class, [
                'label' => 'Prénom',
            ])
            ->add('mail', EmailType::class, [
                'label' => 'E-mail',
                'constraints' => [
                    new Assert\NotBlank([
                        'message' => 'L’email est obligatoire.',
                    ]),
                    new Assert\Email([
                        'message' => 'Veuillez fournir un email valide.',
                    ]),
                ],
            ])
            ->add('password', RepeatedType::class, [
                'type' => PasswordType::class,
                'first_options'  => [
                    'label' => 'Mot de passe',
                    'constraints' => [
                        new Assert\Length([
                            'min' => 6,
                            'minMessage' => 'Le mot de passe doit contenir au moins 6 caractères.',
                        ]),
                    ],
                ],
                'second_options' => [
                    'label' => 'Confirmer le mot de passe',
                ],
                'invalid_message' => 'Les mots de passe ne correspondent pas.',
            ])
            ->add('continuer', SubmitType::class, [
                'label' => 'Continuer',
                'attr' => ['class' => 'btn btn-primary'],
            ]);
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Users::class,
            'constraints' => [
                new UniqueEntity([
                    'fields' => ['mail'],
                    'message' => 'Cet email est déjà utilisé.',
                ]),
            ],
        ]);
    }
}
