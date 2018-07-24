<?php

namespace App\Controller;

use App\Entity\Media;
use App\Entity\MediaVideo;
use App\Services\MediaServices;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class MediaController extends Controller
{
    /**
     * @Route("/mediaDel/{media}")
     */
    public function mediaDel(Media $media, MediaServices $mediaServices)
    {
        $trick = $media->getTrick();

        $mediaServices->mediaDel($media);

        $this->addFlash('success','L\'image a été supprimé');

        return $this->redirectToRoute('app_trick_modify', [
            'trick' => $trick->getId()
        ]);
    }

    /**
     * @Route("/videoDel/{video}")
     */
    public function VideoDel(MediaVideo $video, MediaServices $mediaServices)
    {
        $trick = $video->getTrick();

        $mediaServices->videoDel($video);

        $this->addFlash('success','La vidéo a été supprimé');

        return $this->redirectToRoute('app_trick_modify', [
            'trick' => $trick->getId()
        ]);
    }
}
