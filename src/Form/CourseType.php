<?php

namespace App\Form;

use App\Entity\Course;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;

class CourseType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', TextType::class, [
                'label' => "Название",
                "constraints"   =>  [
                    new Length([
                        'min'   => 5,
                        'minMessage' => 'Название должно быть больше {{ limit }} символов',
                        'max' => 1000,
                    ])
                ]
            ])
            ->add('description', TextType::class, [
                'label' => "Описание",
                "constraints"   =>  [
                    new Length([
                        'min'   => 10,
                        'minMessage' => 'Описание должно быть больше {{ limit }} символов',
                        'max' => 1000,
                    ])
                ]
            ])
            ->add('code', TextType::class, [
                'label' => "Код",
                "constraints"   =>  [
                    new Length([
                        'min'   => 3,
                        'minMessage' => 'код должен быть больше {{ limit }} символов',
                        'max' => 255,
                    ])
                ]
            ])
            ->add('price', TextType::class, [
                'label' => "Цена",
                'mapped' => false,
            ])
            ->add('type', IntegerType::class, [
                'label' => "Тип",
                'mapped' => false,
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            //'data_class' => null,
        ]);
    }
}
