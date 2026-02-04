<?php

namespace App\Form;

use App\Entity\Championship;
use App\Entity\Team;
use App\Entity\TeamChampionShip;
use App\Repository\ChampionshipRepository;
use App\Repository\TeamRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;

class TeamChampionShipFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('championship', EntityType::class, [
                'label' => 'Championnat',
                'class' => Championship::class,
                'choice_label' => function (Championship $championship) {
                    $startDate = $championship->getStartDate()->format('d/m/Y');
                    $endDate = $championship->getEndDate()->format('d/m/Y');
                    return $championship->getName() . ' (' . $startDate . ' - ' . $endDate . ')';
                },
                'placeholder' => 'Sélectionnez un championnat',
                'attr' => [
                    'class' => 'form-control',
                ],
                'query_builder' => function (ChampionshipRepository $er) {
                    return $er->createQueryBuilder('c')
                        ->orderBy('c.startDate', 'DESC')
                        ->addOrderBy('c.name', 'ASC');
                },
                'constraints' => [
                    new NotBlank([
                        'message' => 'Veuillez sélectionner un championnat',
                    ]),
                ],
            ])
            ->add('team', EntityType::class, [
                'label' => 'Équipe',
                'class' => Team::class,
                'choice_label' => function (Team $team) {
                    return $team->getName() . ' (' . $team->getCountry()->getName() . ')';
                },
                'placeholder' => 'Sélectionnez une équipe',
                'attr' => [
                    'class' => 'form-control',
                ],
                'query_builder' => function (TeamRepository $er) {
                    return $er->createQueryBuilder('t')
                        ->join('t.country', 'c')
                        ->orderBy('c.name', 'ASC')
                        ->addOrderBy('t.name', 'ASC');
                },
                'constraints' => [
                    new NotBlank([
                        'message' => 'Veuillez sélectionner une équipe',
                    ]),
                ],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => TeamChampionShip::class,
        ]);
    }
}

