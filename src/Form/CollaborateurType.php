<?php

namespace App\Form;

use App\Entity\Collaborateur;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;

class CollaborateurType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nom',TextType::class,[
                'label' => 'Nom :',
                'constraints' => [
                    new Assert\NotBlank([
                        'message' => "Ce champ ne peut pas être vide ! "
                    ])
                ]
            ])
            ->add('prenom',TextType::class,[
                'label' => 'Prénom :',
                'constraints' => [
                    new Assert\NotBlank([
                        'message' => "Ce champ ne peut pas être vide ! "
                    ])
                ]
            ])
            ->add('email',EmailType::class,[
                'label' => 'Adresse e-mail :',
                'constraints' => [
                    new Assert\NotBlank([
                        'message' => 'L\'adresse e-mail est obligatoire.',
                    ])
                ]
            ])
            ->add('dateEmbauche', DateTimeType::class,[
                'label' => 'Date d\'embauche :',
                'constraints' => [
                    new Assert\NotBlank([
                        'message' => 'La date d\'embauche est obligatoire.',
                    ])
                ]
            ])
            ->add('photo',FileType::class,[
                'label' => 'Photo de profil (Format JPEG ou PNG) :',
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
            'data_class' => Collaborateur::class,
        ]);
    }
}
