<?php
/**
 * Created by PhpStorm.
 * User: Mathieu
 * Date: 24/07/2018
 * Time: 19:33.
 */

namespace App\Service;

use App\Entity\Trick;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;

class TrickService
{
    private $entityManager;

    /**
     * TrickService constructor.
     *
     * @param EntityManagerInterface $entityManager
     */
    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * @param User  $user
     * @param Trick $trick
     *
     * @return bool
     */
    public function add(User $user, Trick $trick)
    {
        $trick->setDate(new \DateTime());
        $trick->setAuthor($user);
        $this->entityManager->persist($trick);
        $this->entityManager->flush();

        return true;
    }

    /**
     * @param Trick $trick
     *
     * @return bool
     */
    public function remove(Trick $trick)
    {
        $this->entityManager->remove($trick);
        $this->entityManager->flush();

        return true;
    }

    /**
     * @param Trick $trick
     */
    public function update(Trick $trick)
    {
        $trick->setDateLastMod(new \DateTime());
        $this->entityManager->persist($trick);
        $this->entityManager->flush();

        return true;
    }

    /**
     * @param EntityRepository $repo
     * @param Trick            $trick
     *
     * @return bool
     */
    public function isExists(EntityRepository $repo, Trick $trick)
    {
        if ($repo->findOneBy(['name' => $trick->getName()])) {
            return true;
        }

        return false;
    }
}
