<?php

namespace App\Repository;

use App\Entity\Chanson;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Chanson>
 *
 * @method Chanson|null find($id, $lockMode = null, $lockVersion = null)
 * @method Chanson|null findOneBy(array $criteria, array $orderBy = null)
 * @method Chanson[]    findAll()
 * @method Chanson[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ChansonRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Chanson::class);
    }
}
