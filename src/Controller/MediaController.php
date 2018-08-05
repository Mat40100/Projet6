<?php

namespace App\Controller;

use App\Entity\Media;
use App\Entity\MediaVideo;
use App\Service\MediaService;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class MediaController extends Controller
{
    /**
     * @Route("/mediaDel/{media}")
     */
    public function mediaDel(Media $media, MediaService $mediaService)
    {
        $trick = $media->getTrick();

        $mediaService->mediaDel($media);

        $this->addFlash('success', 'L\'image a été supprimé');

        return $this->redirectToRoute('app_trick_modify', [
            'trick' => $trick->getId(),
        ]);
    }

    /**
     * @Route("/videoDel/{video}")
     */
    public function VideoDel(MediaVideo $video, MediaService $mediaService)
    {
        $trick = $video->getTrick();

        $mediaService->videoDel($video);

        $this->addFlash('success', 'La vidéo a été supprimé');

        return $this->redirectToRoute('app_trick_modify', [
            'trick' => $trick->getId(),
        ]);
    }
}
