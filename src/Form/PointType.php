<?php
declare(strict_types=1);

namespace App\Form;

use App\Entity\Point;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PointType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('identifier', TextType::class, [
                'label' => false,
                'attr' => ['placeholder' => 'Matricule']
            ])
            ->add('x', NumberType::class, [
                'label' => false,
                'attr' => ['placeholder' => 'La valeur X']
            ])
            ->add('y', NumberType::class, [
                'label' => false,
                'attr' => ['placeholder' => 'La valeur Y']
            ])
            ->add('z', NumberType::class, [
                'required' => false,
                'label' => false,
                'attr' => ['placeholder' => 'La valeur Z']
            ])
            ->add('description', TextareaType::class, [
                'required' => false,
                'label' => false,
                'attr' => ['placeholder' => 'La description']
            ]);

    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Point::class
        ]);
    }
}