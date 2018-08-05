<?php

namespace App\DataFixtures;

use App\Entity\MediaVideo;
use App\Entity\Trick;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;

class VideosFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager)
    {
        $tricks = $manager->getRepository(Trick::class)->findAll();

        $VideosArray = array(
              'https://www.youtube.com/watch?v=8AWdZKMTG3U',
              'https://www.youtube.com/watch?v=gbHU6J6PRRw',
              'https://www.youtube.com/watch?v=4o6vzGE1CUc',
              'https://www.youtube.com/watch?v=PDXPSIz3y2w',
              'https://www.youtube.com/watch?v=G9qlTInKbNE',
          );

        foreach ($tricks as $key => $value) {
            for ($i = 0; $i <= 2; ++$i) {
                $rand = rand(0, 4);
                $video = new MediaVideo();
                $video->setUrl($VideosArray[$rand]);
                $value->addVideo($video);
                $manager->persist($value);
            }
        }

        $manager->flush();
    }

    public function getDependencies()
    {
        return array(TrickFixtures::class);
    }
}
