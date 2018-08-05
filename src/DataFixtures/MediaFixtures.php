<?php

namespace App\DataFixtures;

use App\Entity\Media;
use App\Entity\Trick;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;

class MediaFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager)
    {
        $tricks = $manager->getRepository(Trick::class)->findAll();

        foreach ($tricks as $key => $value) {
            for ($i = 0; $i <= 2; ++$i) {
                $rand = rand(1, 7);
                $media = new Media();
                $media->setExtension('jpg');
                $media->setIdentif($rand);
                $media->setAlt($rand);

                $value->addMedia($media);
            }
            $manager->persist($value);
        }

        $manager->flush();
    }

    public function getDependencies()
    {
        return array(TrickFixtures::class);
    }
}
