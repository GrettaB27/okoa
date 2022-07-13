<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\Form\Extension\Core\Type\FileType;



class EditUserProfileFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nom', TextType::class, [
                'attr' => [
                    'maxLength' => 45
                ]
            ])
            ->add('prenom', TextType::class, [
                'attr' => [
                    'maxLength' => 45
                ]
            ])
            ->add('adresse', TextareaType::class, [
                'attr' => [
                    'maxLength' => 45
                ]
            ])
            ->add('portable', TextType::class, [
                'attr' => [
                    'maxLength' => 10
                ]
            ])
            ->add('email', EmailType::class, [
                'attr' => [
                    'maxLength' => 100
                ]
            ])->add('imageProfile', FileType::class, [
                'label' => '',
                'mapped' => false,
                'required' => false,
                'constraints' => [
                    new File([
                        'mimeTypes' => [
                            'image/jpeg',
                            'image/png',
                        ],
                        'mimeTypesMessage' => 'Please upload a valid Image File',
                    ])
                ],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
