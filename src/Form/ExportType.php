<?php
declare(strict_types=1);

namespace App\Form;

use App\Entity\Point;
use App\Request\ExportPoints;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ExportType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('points', EntityType::class, [
            'class' => Point::class,
            'choice_label' => 'identifier',
            'multiple' => true,
            'expanded' => true
        ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => ExportPoints::class,
            'csrf_protection' => false
        ]);
    }
}