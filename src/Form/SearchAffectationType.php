<?php

namespace App\Form;

use App\Entity\Fonction;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;

class SearchAffectationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('search',TextType::class,[
                'label' => 'Collaborateur : ',
                'required' => false
            ])
            ->add('ville',TextType::class,[
                'label' => 'Ville : ',
                'required' => false
            ])
            ->add('fonction', EntityType::class, [
                'label' => 'Poste : ',
                'placeholder' => 'Choisissez un poste',
                'class' => Fonction::class,
                'choice_label' => 'intitule',
                'required' => false
            ])
            ->add('date',DateTimeType::class,[
                'label' => 'Date d\'affectation : ',
                'required' => false
            ])
            ->add('dateFin',DateTimeType::class,[
                'label' => 'Date de départ : ',
                'required' => false
            ])
            ->add('submit',SubmitType::class,[
                'label' => 'Rechercher',
                'attr' => [
                    'class' => 'button secondary'
                ]
            ]);
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
        ]);
    }
}
