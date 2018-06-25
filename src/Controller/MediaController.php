<?php

namespace App\Controller;

use App\Entity\Media;
use App\Entity\MediaVideo;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class MediaController extends Controller
{
    /**
     * @Route("/mediaDel/{media}", name="mediaDel")
     */
    public function MediaDel(Media $media)
    {
        $em = $this->getDoctrine()->getManager();
        $trick = $media->getTrick();

        $trick->removeMedia($media);
        $em->persist($trick);
        $em->flush();

        return $this->redirectToRoute('modify', [
            'trick' => $trick->getId()
        ]);
    }

    /**
     * @Route("/videoDel/{video}", name="videoDel")
     */
    public function VideoDel(MediaVideo $video){

        $em = $this->getDoctrine()->getManager();
        $trick = $video->getTrick();

        $trick->removeVideo($video);
        $em->persist($trick);
        $em->flush();

        return $this->redirectToRoute('modify', [
            'trick' => $trick->getId()
        ]);

    }
}
