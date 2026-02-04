<?php

namespace App\Repository;

use App\Entity\Championship;
use App\Entity\Country;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Championship>
 */
class ChampionshipRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Championship::class);
    }
    
    public function findByCountry(?Country $country = null): array
    {
        // Si aucun pays n'est sélectionné retourner tous les championnats
        if ($country === null) {
            return $this->findBy([], ['startDate' => 'DESC', 'name' => 'ASC']);
        }

        return $this->createQueryBuilder('c')
            ->distinct()
            ->join('c.teamChampionShips', 'tcs')
            ->join('tcs.team', 't')
            ->where('t.country = :country')
            ->setParameter('country', $country)
            ->orderBy('c.startDate', 'DESC')
            ->addOrderBy('c.name', 'ASC')
            ->getQuery()
            ->getResult();
    }
}

