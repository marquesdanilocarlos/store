<?php

namespace App\Form;

use App\Entity\Category;
use App\Entity\Product;
use NumberFormatter;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\CallbackTransformer;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ProductType extends AbstractType
{

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class, ['label' => 'Nome:', 'help' => 'Nome do produto'])
            ->add('description', TextType::class, ['label' => 'Descrição:'])
            ->add('body', TextareaType::class, ['label' => 'Conteúdo:'])
            ->add('price', TextType::class, ['label' => 'Preço:'])
            ->add(
                'photos',
                FileType::class,
                ['required' => false, 'label' => 'Fotos:', 'mapped' => false, 'multiple' => true]
            )
            ->add('categories', EntityType::class, [
                'class' => Category::class,
                'choice_label' => 'name',
                'multiple' => true,
                'label' => 'Categorias:',
                'placeholder' => 'Selecione uma categoria'
            ])
            ->add('save', SubmitType::class, ['label' => 'Salvar']);

        $builder->setMethod($options['isEdit'] ? 'PUT' : 'POST');

        $builder->get('price')->addModelTransformer(
            new CallbackTransformer(
                function ($price) {
                    $price = $price / 100;
                    return number_format($price, 2, ',', '.');
                },
                function ($price) {
                    $price = floatval(str_replace(['.', ','], ['', '.'], $price));
                    $price = $price * 100;
                    $price = intval($price);
                    return $price;
                }
            )
        );
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Product::class,
            'isEdit' => false
        ]);
    }
}
