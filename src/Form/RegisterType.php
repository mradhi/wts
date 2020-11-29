<?php
declare(strict_types=1);

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\BirthdayType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class RegisterType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('firstName', TextType::class, [
                'attr' => [
                    'placeholder'=> 'Prénom'
                ],
                'label' => false
            ])
            ->add('lastName', TextType::class, [
                'attr' => [
                    'placeholder' => 'Nom de famille'
                ],
                'label' => false
            ])
            ->add('username', EmailType::class, [
                'attr' => [
                    'placeholder' => 'Adresse e-mail'
                ],
                'label' => false
            ])

            ->add('plainPassword', RepeatedType::class, [
                'type' => PasswordType::class,
                'required' => true,
                'invalid_message' => 'The password fields must match.',
                'first_options'  => ['attr' => [
                    'placeholder' => 'Nouveau mot de passe'
                    ],
                    'label' => false
                ],
                'second_options' => ['attr' => [ 
                    'placeholder' => 'Confirmer mot de passe'
                    ],
                    'label' => false
                ],
            ])

            ->add('birthDate', BirthdayType::class, [
                'label' => 'Date de naissance'
            ])
            ->add('profession', ChoiceType::class, [
                'choices' => [
                    'Ingénieur' => 'Ingénieur',
                    'Technicien' => 'Technicien'
                ],
                'multiple' => false,
                'expanded' => true
            ])
            ->add('gender', ChoiceType::class, [
                'label' => 'Sexe',
                'choices' => [
                    'Homme' => 'Homme',
                    'Femme' => 'Femme'
                ],
                'multiple' => false,
                'expanded' => true
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => User::class,
            'csrf_protection' => false
        ]);
    }
}