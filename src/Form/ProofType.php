<?php

namespace App\Form;

use App\Entity\Proof;
use App\Entity\Resume;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ProofType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('images')
            ->add('dateUpload', null, [
                'widget' => 'single_text',
            ])
            ->add('resume', EntityType::class, [
                'class' => Resume::class,
                'choice_label' => 'id',
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Proof::class,
        ]);
    }
}
