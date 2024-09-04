<?php

namespace App\Form;

use App\Entity\Collaborateur;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;

class RegistrationFormType extends AbstractType
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
            ->add('dateEmbauche',DateTimeType::class,[
                'label' => 'Date d\'embauche :',
                'constraints' => [
                    new Assert\NotBlank([
                        'message' => 'La date d\'embauche est obligatoire.',
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
            ->add('plainPassword', PasswordType::class, [
                // instead of being set onto the object directly,
                // this is read and encoded in the controller
                'mapped' => false,
                'attr' => ['autocomplete' => 'new-password'],
                'constraints' => [
                    new NotBlank([
                        'message' => 'Définissez un mot de passe',
                    ]),
                    new Length([
                        'min' => 6,
                        'minMessage' => 'Votre mot de passe doit faire un minimum de {{ limit }} caractères',
                        'max' => 20,
                    ]),
                ],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Collaborateur::class,
        ]);
    }
}
