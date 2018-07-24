<?php
/**
 * Created by PhpStorm.
 * User: Mathieu
 * Date: 24/07/2018
 * Time: 18:05
 */

namespace App\Services;


use App\Entity\Picture;
use Doctrine\ORM\EntityManagerInterface;

class PictureServices
{
    private $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    public function save(Picture $picture)
    {
        $this->em->persist($picture);

        $this->em->flush();
    }

    public function delete(Picture $picture)
    {
        $this->em->remove($picture);

        $this->em->flush();
    }

}