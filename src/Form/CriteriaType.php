<?php

namespace App\Form;

use App\Entity\Criteria;
use App\Entity\Post;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\ArrayType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CriteriaType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
        ->add('experience', TextType::class, [
            'label' => 'Experience',
            'required' => true,
        ])
        ->add('level', TextType::class, [
            'label' => 'Level',
            'required' => false,
        ])
        ->add('certifs', CollectionType::class, [
            'label' => false,
            'entry_type' => TextType::class, // Chaque certification est un champ texte
            'allow_add' => true, // Ajout dynamique d'éléments
            'allow_delete' => true, // Suppression d'éléments
            'by_reference' => false,
            'prototype' => true,
        ])
        ->add('languages', CollectionType::class, [
            'label' => false,
            'entry_type' => TextType::class, // Chaque langue est un champ texte
            'allow_add' => true,
            'allow_delete' => true,
            'by_reference' => false,
            'prototype' => true,
        ])
        ->add('skills', CollectionType::class, [
            'label' => false,
            'entry_type' => TextType::class, // Chaque compétence est un champ texte
            'allow_add' => true,
            'allow_delete' => true,
            'by_reference' => false,
            'prototype' => true,
        ]);
       
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Criteria::class,
        ]);
    }
}
