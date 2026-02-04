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
        $qb = $this->createQueryBuilder('c')
            ->distinct()
            ->join('c.teamChampionShips', 'tcs')
            ->join('tcs.team', 't')
            ->orderBy('c.startDate', 'DESC')
            ->addOrderBy('c.name', 'ASC');

        if ($country !== null) {
            $qb->where('t.country = :country')
               ->setParameter('country', $country);
        }

        return $qb->getQuery()->getResult();
    }
}

