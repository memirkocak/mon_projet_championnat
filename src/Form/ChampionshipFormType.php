<?php

namespace App\Form;

use App\Entity\Championship;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\GreaterThan;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\PositiveOrZero;

class ChampionshipFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class, [
                'label' => 'Nom du championnat',
                'attr' => [
                    'class' => 'form-control',
                    'placeholder' => 'Ex: Ligue 1'
                ],
                'constraints' => [
                    new NotBlank([
                        'message' => 'Veuillez entrer un nom de championnat',
                    ]),
                ],
            ])
            ->add('startDate', DateType::class, [
                'label' => 'Date de début',
                'widget' => 'single_text',
                'attr' => [
                    'class' => 'form-control',
                ],
                'constraints' => [
                    new NotBlank([
                        'message' => 'Veuillez entrer une date de début',
                    ]),
                ],
            ])
            ->add('endDate', DateType::class, [
                'label' => 'Date de fin',
                'widget' => 'single_text',
                'attr' => [
                    'class' => 'form-control',
                ],
                'constraints' => [
                    new NotBlank([
                        'message' => 'Veuillez entrer une date de fin',
                    ]),
                    new GreaterThan([
                        'propertyPath' => 'parent.all[startDate].data',
                        'message' => 'La date de fin doit être postérieure à la date de début',
                    ]),
                ],
            ])
            ->add('wonPoint', IntegerType::class, [
                'label' => 'Points pour une victoire',
                'attr' => [
                    'class' => 'form-control',
                    'min' => 0,
                ],
                'constraints' => [
                    new NotBlank([
                        'message' => 'Veuillez entrer le nombre de points pour une victoire',
                    ]),
                    new PositiveOrZero([
                        'message' => 'Le nombre de points doit être positif ou zéro',
                    ]),
                ],
            ])
            ->add('lostPoint', IntegerType::class, [
                'label' => 'Points pour une défaite',
                'attr' => [
                    'class' => 'form-control',
                    'min' => 0,
                ],
                'constraints' => [
                    new NotBlank([
                        'message' => 'Veuillez entrer le nombre de points pour une défaite',
                    ]),
                    new PositiveOrZero([
                        'message' => 'Le nombre de points doit être positif ou zéro',
                    ]),
                ],
            ])
            ->add('drawPoint', IntegerType::class, [
                'label' => 'Points pour un match nul',
                'attr' => [
                    'class' => 'form-control',
                    'min' => 0,
                ],
                'constraints' => [
                    new NotBlank([
                        'message' => 'Veuillez entrer le nombre de points pour un match nul',
                    ]),
                    new PositiveOrZero([
                        'message' => 'Le nombre de points doit être positif ou zéro',
                    ]),
                ],
            ])
            ->add('typeRanking', TextType::class, [
                'label' => 'Type de classement',
                'attr' => [
                    'class' => 'form-control',
                    'placeholder' => 'Ex: Points, Différence de buts'
                ],
                'constraints' => [
                    new NotBlank([
                        'message' => 'Veuillez entrer un type de classement',
                    ]),
                ],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Championship::class,
        ]);
    }
}

