<?php

namespace App\Form;

use App\Entity\Country;
use App\Entity\Team;
use App\Repository\CountryRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\Validator\Constraints\NotBlank;

class TeamFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class, [
                'label' => 'Nom de l\'équipe',
                'attr' => [
                    'class' => 'form-control',
                    'placeholder' => 'Ex: Paris Saint-Germain'
                ],
                'constraints' => [
                    new NotBlank([
                        'message' => 'Veuillez entrer un nom d\'équipe',
                    ]),
                ],
            ])
            ->add('creationDate', DateType::class, [
                'label' => 'Date de création',
                'widget' => 'single_text',
                'attr' => [
                    'class' => 'form-control',
                ],
                'constraints' => [
                    new NotBlank([
                        'message' => 'Veuillez entrer une date de création',
                    ]),
                ],
            ])
            ->add('stade', TextType::class, [
                'label' => 'Stade',
                'attr' => [
                    'class' => 'form-control',
                    'placeholder' => 'Ex: Parc des Princes'
                ],
                'constraints' => [
                    new NotBlank([
                        'message' => 'Veuillez entrer un nom de stade',
                    ]),
                ],
            ])
            ->add('logo', FileType::class, [
                'label' => 'Logo de l\'équipe',
                'required' => false,
                'mapped' => false,
                'attr' => [
                    'class' => 'form-control',
                    'accept' => 'image/*'
                ],
                'constraints' => [
                    new File([
                        'maxSize' => '2M',
                        'mimeTypes' => [
                            'image/jpeg',
                            'image/png',
                            'image/gif',
                            'image/webp',
                        ],
                        'mimeTypesMessage' => 'Veuillez uploader une image valide (JPEG, PNG, GIF ou WebP)',
                    ])
                ],
            ])
            ->add('president', TextType::class, [
                'label' => 'Président',
                'attr' => [
                    'class' => 'form-control',
                    'placeholder' => 'Nom du président'
                ],
                'constraints' => [
                    new NotBlank([
                        'message' => 'Veuillez entrer le nom du président',
                    ]),
                ],
            ])
            ->add('coach', TextType::class, [
                'label' => 'Entraîneur',
                'attr' => [
                    'class' => 'form-control',
                    'placeholder' => 'Nom de l\'entraîneur'
                ],
                'constraints' => [
                    new NotBlank([
                        'message' => 'Veuillez entrer le nom de l\'entraîneur',
                    ]),
                ],
            ])
            ->add('country', EntityType::class, [
                'label' => 'Pays',
                'class' => Country::class,
                'choice_label' => 'name',
                'placeholder' => 'Sélectionnez un pays',
                'attr' => [
                    'class' => 'form-control',
                ],
                'query_builder' => function (CountryRepository $er) {
                    return $er->createQueryBuilder('c')
                        ->orderBy('c.name', 'ASC');
                },
                'constraints' => [
                    new NotBlank([
                        'message' => 'Veuillez sélectionner un pays',
                    ]),
                ],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Team::class,
        ]);
    }
}

