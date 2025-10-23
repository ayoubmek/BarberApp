<?php

namespace App\Form;

use App\Entity\Service;
use App\Entity\Category;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ServiceType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class, [
                'label' => 'Service Name',
                'attr' => [
                    'placeholder' => 'Enter service name',
                    'class' => 'form-control'
                ],
            ])
            ->add('image', FileType::class, [
                'label' => 'Image (optional)',
                'required' => false,
                'mapped' => false, // file uploads handled manually
            ])
            ->add('price', MoneyType::class, [
                'label' => 'Price',
                'currency' => 'USD', // change this if you use another currency
                'attr' => ['class' => 'form-control']
            ])
            ->add('duration', IntegerType::class, [
                'label' => 'Duration (minutes)',
                'attr' => [
                    'placeholder' => 'Enter duration in minutes',
                    'class' => 'form-control'
                ],
            ])
            ->add('category', EntityType::class, [
                'class' => Category::class,
                'choice_label' => 'name',
                'label' => 'Category',
                'placeholder' => 'Select a category',
                'required' => false,
                'attr' => ['class' => 'form-select']
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Service::class,
        ]);
    }
}
    