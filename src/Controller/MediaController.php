<?php

namespace App\Controller;

use App\Entity\Media;
use App\Entity\MediaVideo;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class MediaController extends Controller
{
    /**
     * @Route("/mediaDel/{media}")
     */
    public function mediaDel(Media $media)
    {
        $em = $this->getDoctrine()->getManager();
        $trick = $media->getTrick();

        $trick->removeMedia($media);
        $em->flush();

        $this->addFlash('success','L\'image a été supprimé');

        return $this->redirectToRoute('app_trick_modify', [
            'trick' => $trick->getId()
        ]);
    }

    /**
     * @Route("/videoDel/{video}")
     */
    public function VideoDel(MediaVideo $video)
    {
        $em = $this->getDoctrine()->getManager();
        $trick = $video->getTrick();

        $trick->removeVideo($video);
        $em->flush();

        $this->addFlash('success','La vidéo a été supprimé');

        return $this->redirectToRoute('app_trick_modify', [
            'trick' => $trick->getId()
        ]);

    }
}
