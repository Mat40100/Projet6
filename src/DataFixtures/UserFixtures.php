<?php

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;

class UserFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {
        $listNames = array('Alexandre', 'Marine', 'Anna');

        foreach ($listNames as $name) {
            // On crée l'utilisateur
            $user = new User();

            // Le nom d'utilisateur et le mot de passe sont identiques pour l'instant
            $user->setUsername($name);
            $user->setPassword($name);
            $user->setLastName($name);
            $randomNum = random_bytes(10);
            $user->setRecoveryToken(hash('sha256', $randomNum));
            $user->setFirstName($name);
            $user->setEmail($name);
            $user->setRoles('ROLE_USER');
            $manager->persist($user);
        }

        $manager->flush();
    }
}
