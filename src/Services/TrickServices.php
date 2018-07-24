<?php
/**
 * Created by PhpStorm.
 * User: Mathieu
 * Date: 24/07/2018
 * Time: 19:33
 */

namespace App\Services;


use App\Entity\Trick;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;

class TrickServices
{

    private $em;


    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    public function add(User $user,Trick $trick)
    {
        $trick->setDate(new \DateTime());
        $trick->setAuthor($user);
        $this->em->persist($trick);
        $this->em->flush();

        return true;
    }

    public function remove(Trick $trick)
    {
        $this->em->remove($trick);
        $this->em->flush();

        return true;
    }

    public function update(Trick $trick)
    {
        $trick->setDateLastMod(new \DateTime());
        $this->em->flush();
    }

    public function isExists(EntityRepository $repo, Trick $trick)
    {
        if($repo->findOneBy(['name' => $trick->getName()])){
            return true;
        }
    }
}