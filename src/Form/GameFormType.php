<?php

namespace App\Form;

use App\Entity\Day;
use App\Entity\Game;
use App\Entity\Team;
use App\Repository\DayRepository;
use App\Repository\TeamRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\PositiveOrZero;

class GameFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('day', EntityType::class, [
                'label' => 'Journée',
                'class' => Day::class,
                'choice_label' => function (Day $day) {
                    return $day->getChampionship()->getName() . ' - Journée ' . $day->getNumber();
                },
                'placeholder' => 'Sélectionnez une journée',
                'attr' => [
                    'class' => 'form-control',
                ],
                'query_builder' => function (DayRepository $er) {
                    return $er->createQueryBuilder('d')
                        ->join('d.championship', 'c')
                        ->orderBy('c.name', 'ASC')
                        ->addOrderBy('d.number', 'ASC');
                },
                'constraints' => [
                    new NotBlank([
                        'message' => 'Veuillez sélectionner une journée',
                    ]),
                ],
            ])
            ->add('team1', EntityType::class, [
                'label' => 'Équipe 1',
                'class' => Team::class,
                'choice_label' => 'name',
                'placeholder' => 'Sélectionnez l\'équipe 1',
                'attr' => [
                    'class' => 'form-control',
                ],
                'query_builder' => function (TeamRepository $er) {
                    return $er->createQueryBuilder('t')
                        ->orderBy('t.name', 'ASC');
                },
                'constraints' => [
                    new NotBlank([
                        'message' => 'Veuillez sélectionner l\'équipe 1',
                    ]),
                ],
            ])
            ->add('team1Point', IntegerType::class, [
                'label' => 'Points équipe 1',
                'attr' => [
                    'class' => 'form-control',
                    'min' => 0,
                ],
                'constraints' => [
                    new NotBlank([
                        'message' => 'Veuillez entrer les points de l\'équipe 1',
                    ]),
                    new PositiveOrZero([
                        'message' => 'Le nombre de points doit être positif ou zéro',
                    ]),
                ],
            ])
            ->add('team2', EntityType::class, [
                'label' => 'Équipe 2',
                'class' => Team::class,
                'choice_label' => 'name',
                'placeholder' => 'Sélectionnez l\'équipe 2',
                'attr' => [
                    'class' => 'form-control',
                ],
                'query_builder' => function (TeamRepository $er) {
                    return $er->createQueryBuilder('t')
                        ->orderBy('t.name', 'ASC');
                },
                'constraints' => [
                    new NotBlank([
                        'message' => 'Veuillez sélectionner l\'équipe 2',
                    ]),
                ],
            ])
            ->add('team2Point', IntegerType::class, [
                'label' => 'Points équipe 2',
                'attr' => [
                    'class' => 'form-control',
                    'min' => 0,
                ],
                'constraints' => [
                    new NotBlank([
                        'message' => 'Veuillez entrer les points de l\'équipe 2',
                    ]),
                    new PositiveOrZero([
                        'message' => 'Le nombre de points doit être positif ou zéro',
                    ]),
                ],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Game::class,
        ]);
    }
}

