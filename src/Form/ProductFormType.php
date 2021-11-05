<?php

namespace App\Form;

use App\Entity\Category;
use App\Entity\Product;
use Doctrine\DBAL\Types\FloatType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ProductFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('category', EntityType::class, [
                'class' => Category::class,
                'choice_label' => 'name',
                'empty_data' => '',
                'required' => true,
                'placeholder' => 'Catégorie'
            ])
            ->add('name', TextType::class)
            ->add('shortDescription', TextType::class)
            ->add('description', TextareaType::class, ['required' => false])
            ->add('transactionType', ChoiceType::class, [
                'choices' => [
                    'Achat Immédiat' => 'A',
                    'Enchère' => 'E',
                ]
            ])
            ->add('timeLimit', NumberType::class, ['required' => false])
            ->add('price', NumberType::class)
            ->add('images', FileType::class, [
                'label' => false,
                'multiple' => true,
                'mapped' => false,
                'required' => false
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Product::class,
        ]);
    }
}
