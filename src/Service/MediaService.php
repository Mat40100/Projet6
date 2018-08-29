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
    private $entityManager;

    /**
     * MediaService constructor.
     *
     * @param EntityManagerInterface $entityManager
     */
    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * @param Media $media
     *
     * @return bool
     */
    public function mediaAdd(Media $media)
    {
        $this->entityManager->persist($media);
        $this->entityManager->flush();

        return true;
    }

    /**
     * @param MediaVideo $mediaVideo
     *
     * @return bool
     */
    public function videoAdd(MediaVideo $mediaVideo)
    {
        $this->entityManager->persist($mediaVideo);
        $this->entityManager->flush();

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
        $this->entityManager->flush();

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
        $this->entityManager->flush();

        return true;
    }
}
