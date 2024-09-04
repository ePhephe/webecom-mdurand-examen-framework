<?php

namespace App\Form;

use App\Entity\Restaurant;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

class RestaurantType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nom',TextType::class,[
                'label' => 'Nom du restaurant :',
                'constraints' => [
                    new Assert\NotBlank([
                        'message' => "Ce champ ne peut pas être vide ! "
                    ])
                ]
            ])
            ->add('adresse',TextType::class,[
                'label' => 'Adresse :',
                'constraints' => [
                    new Assert\NotBlank([
                        'message' => "Ce champ ne peut pas être vide ! "
                    ])
                ]
            ])
            ->add('code_postal',TextType::class,[
                'label' => 'Code postal :',
                'constraints' => [
                    new Assert\NotBlank([
                        'message' => "Ce champ ne peut pas être vide ! "
                    ]),
                    new Assert\Length([
                        'min' => 5,
                        'max' => 5,
                        'exactMessage' => 'Le code postal doit contenir exactement {{ limit }} chiffres.',
                    ])
                ]
            ])
            ->add('ville',TextType::class,[
                'label' => 'Ville :',
                'constraints' => [
                    new Assert\NotBlank([
                        'message' => "Ce champ ne peut pas être vide ! "
                    ])
                ]
            ])
            ->add('photo',FileType::class,[
                'label' => 'Photo du restaurant (Format JPEG ou PNG) :',
                'mapped' => false,
                'required' => false,
                'constraints' => [
                    new Assert\File([
                        'maxSize' => '2M',
                        'mimeTypes' => [
                            'image/jpeg',
                            'image/png',
                        ],
                        'mimeTypesMessage' => 'Merci d\'uploader une image valide (JPEG or PNG) de maximum 2Mo',
                    ])
                ]
            ])
            ->add('Enregistrer',SubmitType::class);
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Restaurant::class,
        ]);
    }
}
