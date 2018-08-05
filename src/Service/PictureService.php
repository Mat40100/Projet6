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
    private $em;

    /**
     * PictureService constructor.
     *
     * @param EntityManagerInterface $em
     */
    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    /**
     * @param Picture $picture
     */
    public function save(Picture $picture)
    {
        $this->em->persist($picture);

        $this->em->flush();
    }

    /**
     * @param Picture $picture
     */
    public function delete(Picture $picture)
    {
        $this->em->remove($picture);

        $this->em->flush();
    }
}
