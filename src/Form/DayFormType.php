<?php

namespace App\Form;

use App\Entity\Championship;
use App\Entity\Day;
use App\Repository\ChampionshipRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;

class DayFormType extends AbstractType
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
            ->add('number', TextType::class, [
                'label' => 'Numéro de la journée',
                'attr' => [
                    'class' => 'form-control',
                    'placeholder' => 'Ex: 1, 2, Journée 1'
                ],
                'constraints' => [
                    new NotBlank([
                        'message' => 'Veuillez entrer un numéro de journée',
                    ]),
                ],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Day::class,
        ]);
    }
}

