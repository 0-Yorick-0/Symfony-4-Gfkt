<?php

namespace App\Form;

use App\Entity\PropertySearch;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

class PropertySearchType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('maxPrice', IntegerType::class, [
                'required' => false,
                'label'    => false,
                'attr'     => [
                    'placeholder' => 'Budget Max'
                ]
            ])
            ->add('minSurface', IntegerType::class, [
                'required' => false,
                'label'    => false,
                'attr'     => [
                    'placeholder' => 'Surface minimale'
                ]
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class'         => PropertySearch::class,
            'translation_domain' => 'forms',
            'method'             => 'get',
            'csrf_protection'    => false,
        ]);
    }

    /**
     * Astuce permettant d'afficher des paramètres propres dans l'url
     */
    public function getBlockPrefix()
    {
        return '';
    }
}
