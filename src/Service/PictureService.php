<?php
/**
 * Created by PhpStorm.
 * User: Mathieu
 * Date: 24/07/2018
 * Time: 18:05.
 */

namespace App\Service;

use App\Entity\Picture;
use Doctrine\ORM\EntityManagerInterface;

class PictureService
{
    private $entityManager;

    /**
     * PictureService constructor.
     *
     * @param EntityManagerInterface $entityManager
     */
    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * @param Picture $picture
     */
    public function save(Picture $picture)
    {
        $this->entityManager->persist($picture);

        $this->entityManager->flush();
    }

    /**
     * @param Picture $picture
     */
    public function delete(Picture $picture)
    {
        $this->entityManager->remove($picture);

        $this->entityManager->flush();
    }
}
