<?php

namespace App\Repository;

use App\Entity\SiteBacklog;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method SiteBacklog|null find($id, $lockMode = null, $lockVersion = null)
 * @method SiteBacklog|null findOneBy(array $criteria, array $orderBy = null)
 * @method SiteBacklog[]    findAll()
 * @method SiteBacklog[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class SiteBacklogRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, SiteBacklog::class);
    }

    /*
    public function findBySomething($value)
    {
        return $this->createQueryBuilder('s')
            ->where('s.something = :value')->setParameter('value', $value)
            ->orderBy('s.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    public function getUnvisitedWebSites($maxResults)
    {
        return $this->createQueryBuilder('s')
            ->where('s.visited = :false')->setParameter('false', false)
            ->orderBy('s.id', 'ASC')
            ->setMaxResults($maxResults)
            ->getQuery()
            ->getResult()
        ;
    }
}
