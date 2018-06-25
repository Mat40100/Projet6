<?php

namespace App\Repository;

use App\Entity\MediaVideo;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method MediaVideo|null find($id, $lockMode = null, $lockVersion = null)
 * @method MediaVideo|null findOneBy(array $criteria, array $orderBy = null)
 * @method MediaVideo[]    findAll()
 * @method MediaVideo[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class MediaVideoRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, MediaVideo::class);
    }

//    /**
//     * @return MediaVideo[] Returns an array of MediaVideo objects
//     */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('m')
            ->andWhere('m.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('m.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?MediaVideo
    {
        return $this->createQueryBuilder('m')
            ->andWhere('m.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
