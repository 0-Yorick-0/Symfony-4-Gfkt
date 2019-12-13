<?php

namespace App\Form;

use App\Entity\Property;
use App\Entity\Tag;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\FileType;

/**
 * @see https://symfony.com/doc/current/reference/forms/types/choice.html#example-usage
 */
class PropertyType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('title')
            ->add('description')
            ->add('surface')
            ->add('rooms')
            ->add('bedrooms')
            ->add('floor')
            ->add('price')
            ->add('heat', ChoiceType::class, [
                'choices' => $this->getChoices()
            ])
            ->add('tags', EntityType::class, [
                'class'        => Tag::class,
                'choice_label' => 'name',
                'multiple'     => true,
                'required'     => false
            ])
            ->add('imageFile', FileType::class, [
                'required' => false,
                'help' => 'Uniquement du jpg/jpeg'
            ])
            ->add('city')
            ->add('address')
            ->add('postal_code')
            ->add('sold')
            // ->add('created_at')
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Property::class,
            'translation_domain' => 'forms'
        ]);
    }

    private function getChoices()
    {
        $output = [];
        foreach (Property::HEAT as $key => $value) {
            $output[$value] = $key;
        }

        return $output;
    }

}
