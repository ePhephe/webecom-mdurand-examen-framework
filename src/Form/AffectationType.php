<?php

namespace App\Form;

use App\Entity\Fonction;
use App\Entity\Restaurant;
use App\Entity\Affectation;
use App\Entity\Collaborateur;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;

class AffectationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('collaborateur', EntityType::class, [
                'class' => Collaborateur::class,
                'choice_label' => 'email',
            ])
            ->add('fonction', EntityType::class, [
                'class' => Fonction::class,
                'choice_label' => 'intitule',
            ])
            ->add('restaurant', EntityType::class, [
                'class' => Restaurant::class,
                'choice_label' => 'nom',
            ])
            ->add('dateDebut', DateTimeType::class, [
                'label' => 'Date d\'affectation : '
            ])
            ->add('dateFin', DateTimeType::class, [
                'label' => 'Date de sortie : ',
                'required' => false
            ])
            ->add('submit', SubmitType::class, [
                'label' => 'Enregistrer',
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Affectation::class,
        ]);
    }
}
