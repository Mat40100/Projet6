<?php

namespace App\Repository;

use App\Entity\ImageUrl;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method ImageUrl|null find($id, $lockMode = null, $lockVersion = null)
 * @method ImageUrl|null findOneBy(array $criteria, array $orderBy = null)
 * @method ImageUrl[]    findAll()
 * @method ImageUrl[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ImageUrlRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, ImageUrl::class);
    }

//    /**
//     * @return ImageUrl[] Returns an array of ImageUrl objects
//     */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('i')
            ->andWhere('i.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('i.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?ImageUrl
    {
        return $this->createQueryBuilder('i')
            ->andWhere('i.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
