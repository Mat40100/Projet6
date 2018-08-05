<?php
/**
 * Created by PhpStorm.
 * User: Mathieu
 * Date: 24/07/2018
 * Time: 19:50.
 */

namespace App\Service;

use App\Entity\Media;
use App\Entity\MediaVideo;
use Doctrine\ORM\EntityManagerInterface;

class MediaService
{
    private $em;

    /**
     * MediaService constructor.
     *
     * @param EntityManagerInterface $em
     */
    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    /**
     * @param Media $media
     *
     * @return bool
     */
    public function mediaAdd(Media $media)
    {
        $this->em->persist($media);
        $this->em->flush();

        return true;
    }

    /**
     * @param MediaVideo $mediaVideo
     *
     * @return bool
     */
    public function videoAdd(MediaVideo $mediaVideo)
    {
        $this->em->persist($mediaVideo);
        $this->em->flush();

        return true;
    }

    /**
     * @param Media $media
     *
     * @return bool
     */
    public function mediaDel(Media $media)
    {
        $trick = $media->getTrick();

        $trick->removeMedia($media);
        $this->em->flush();

        return true;
    }

    /**
     * @param MediaVideo $mediaVideo
     *
     * @return bool
     */
    public function videoDel(MediaVideo $mediaVideo)
    {
        $trick = $mediaVideo->getTrick();

        $trick->removeVideo($mediaVideo);
        $this->em->flush();

        return true;
    }
}
