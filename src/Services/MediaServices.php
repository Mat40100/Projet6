<?php
/**
 * Created by PhpStorm.
 * User: Mathieu
 * Date: 24/07/2018
 * Time: 19:50
 */

namespace App\Services;


use App\Entity\Media;
use App\Entity\MediaVideo;
use Doctrine\ORM\EntityManagerInterface;

class MediaServices
{

    private $em;


    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    public function mediaDel(Media $media)
    {
        $trick = $media->getTrick();

        $trick->removeMedia($media);
        $this->em->flush();

        return true;
    }

    public function videoDel(MediaVideo $mediaVideo)
    {
        $trick = $mediaVideo->getTrick();

        $trick->removeVideo($mediaVideo);
        $this->em->flush();

        return true;
    }

}