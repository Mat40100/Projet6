<?php
/**
 * Created by PhpStorm.
 * User: Mathieu
 * Date: 24/07/2018
 * Time: 19:33
 */

namespace App\Service;


use App\Entity\Trick;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;

class TrickService
{

    private $em;


    /**
     * TrickService constructor.
     * @param EntityManagerInterface $em
     */
    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    /**
     * @param User $user
     * @param Trick $trick
     * @return bool
     */
    public function add(User $user,Trick $trick)
    {
        $trick->setDate(new \DateTime());
        $trick->setAuthor($user);
        $this->em->persist($trick);
        $this->em->flush();

        return true;
    }

    /**
     * @param Trick $trick
     * @return bool
     */
    public function remove(Trick $trick)
    {
        $this->em->remove($trick);
        $this->em->flush();

        return true;
    }

    /**
     * @param Trick $trick
     */
    public function update(Trick $trick)
    {
        $trick->setDateLastMod(new \DateTime());
        $this->em->flush();
    }

    /**
     * @param EntityRepository $repo
     * @param Trick $trick
     * @return bool
     */
    public function isExists(EntityRepository $repo, Trick $trick)
    {
        if($repo->findOneBy(['name' => $trick->getName()])){
            return true;
        }
    }
}