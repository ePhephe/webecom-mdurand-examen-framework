<?php

namespace App\Form;

use App\Entity\Collaborateur;
use Symfony\Component\Form\FormError;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;

class ProfilType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nom', TextType::class,[
                'label' => 'Nom :',
                'constraints' => [
                    new Assert\NotBlank([
                        'message' => 'Le nom ne peut pas être vide.',
                    ])
                ]
            ])
            ->add('prenom', TextType::class,[
                'label' => 'Prénom :',
                'constraints' => [
                    new Assert\NotBlank([
                        'message' => 'Le prénom ne peut pas être vide.',
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
                'label' => 'Mot de passe :',
                'mapped' => false,
                'required' => false,
                'attr' => ['autocomplete' => 'new-password'],
                'constraints' => [
                    new Assert\Length([
                        'min' => 6,
                        'minMessage' => 'Votre mot de passe doit faire minimum {{ limit }} caractères',
                        // max length allowed by Symfony for security reasons
                        'max' => 4096,
                    ]),
                ],
            ])
            ->add('confirmPassword', PasswordType::class, [
                'label' => 'Confirmez le mot de passe :',
                'mapped' => false,
                'required' => false,
            ])
            ->add('photo',FileType::class,[
                'label' => 'Photo du collaborateur (Format JPEG ou PNG) :',
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
            ->addEventListener(
                FormEvents::POST_SUBMIT,
                function (FormEvent $event) {
                    $form = $event->getForm();

                    $plainPassword = $form->get('plainPassword')->getData();
                    $confirmPassword = $form->get('confirmPassword')->getData();

                    if ($plainPassword !== $confirmPassword) {
                        $form->get('confirmPassword')->addError(new FormError('Les mots de passe doivent correspondre.'));
                    }
                }
            )
            ->add('Enregistrer',SubmitType::class);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Collaborateur::class
        ]);
    }
}
