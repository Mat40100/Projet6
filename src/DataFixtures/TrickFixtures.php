<?php

namespace App\DataFixtures;

use App\Entity\Category;
use App\Entity\Trick;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;

class TrickFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {
        $users = $manager->getRepository(User::class)->findBy([], array());
        $userCount = count($users);
        $categories = $manager->getRepository(Category::class)->findAll();

        $listTrick = array(
            '180' => 'saut avec une rotation d\'un demi-tour (souvent abrégé par le sens de rotation. Par exemple on dit qu\'on réalise un back(side) pour dire un 180° backside)',
            'Aerial' => 'Saut droit sur un kicker ou saut avec une rotation d’environ 120° en half-pipe',
            'Air to fakie' => 'Généralement employé en pipe. Décrit un saut droit. C\'est-à-dire que l’on saute dans son stance normal et que l’on replaque en fakie.',
            'Alley oop' => 'Rotation effectuée en half-pipe dont le sens de rotation est inverse au mur. C\'est-à-dire rotation frontside sur un mur backside ou rotation backside sur un mur frontside.',
            'Nose grab' => 'figure qui consiste à attraper le nose (l\'extrémité) du surf pendant un saut',
            'Rocket-air' => 'en l\'air, amener la planche à la verticale, saisir la spatule avec la main avant, plier la jambe avant et tendre la jambe arrière',
            'Stiffy' => 'jambes tendues pendant un saut',
            'Indy nose bone' => 'trick où l\'on tend la jambe arrière',
            '1440' => 'un saut avec une rotation de quatre tours complets (souvent abrégé 1004 ou 14)',
            'Backflip' => 'Saut périlleux arrière effectué dans le sens de la board et non dans le sens du corps. (exemple : backflip en shifty, puis switch double backflip)',
            '182' => 'saut avec une rotation d\'un demi-tour (souvent abrégé par le sens de rotation. Par exemple on dit qu\'on réalise un back(side) pour dire un 180° backside)',
            'Aeial' => 'Saut droit sur un kicker ou saut avec une rotation d’environ 120° en half-pipe',
            'Airto fakie' => 'Généralement employé en pipe. Décrit un saut droit. C\'est-à-dire que l’on saute dans son stance normal et que l’on replaque en fakie.',
            'Alle oop' => 'Rotation effectuée en half-pipe dont le sens de rotation est inverse au mur. C\'est-à-dire rotation frontside sur un mur backside ou rotation backside sur un mur frontside.',
            'Nosegrab' => 'figure qui consiste à attraper le nose (l\'extrémité) du surf pendant un saut',
            'Roket-air' => 'en l\'air, amener la planche à la verticale, saisir la spatule avec la main avant, plier la jambe avant et tendre la jambe arrière',
            'Stify' => 'jambes tendues pendant un saut',
            'Ind nose bone' => 'trick où l\'on tend la jambe arrière',
            '140' => 'un saut avec une rotation de quatre tours complets (souvent abrégé 1004 ou 14)',
            'Bakflip' => 'Saut périlleux arrière effectué dans le sens de la board et non dans le sens du corps. (exemple : backflip en shifty, puis switch double backflip)',
        );

        foreach ($listTrick as $key => $value) {
            $random = rand(0, $userCount - 1);
            $user = $users[$random];
            $trick = new Trick();
            $trick->setAuthor($user);
            $trick->setName($key);
            $trick->setDescription($value);
            $trick->setDate(new \DateTime());
            $category = array_rand($categories);
            $trick->addCategory($categories[$category]);

            $manager->persist($trick);
        }

        $manager->flush();
    }

    public function getDependencies()
    {
        return array(UserFixtures::class, CategoryFixtures::class);
    }
}
