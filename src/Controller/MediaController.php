<?php

namespace App\Controller;

use App\Entity\Media;
use App\Entity\MediaVideo;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class MediaController extends Controller
{
    /**
     * @param Media $media
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     * @Route("/mediaDel/{media}", name="mediaDel")
     */
    public function MediaDel(Media $media)
    {
        $em = $this->getDoctrine()->getManager();
        $trick = $media->getTrick();

        $trick->removeMedia($media);
        $em->persist($trick);
        $em->flush();

        $this->addFlash('success','L\'image a été supprimé');

        return $this->redirectToRoute('modify', [
            'trick' => $trick->getId()
        ]);
    }

    /**
     * @Route("/videoDel/{video}", name="videoDel")
     * @param MediaVideo $video
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function VideoDel(MediaVideo $video){

        $em = $this->getDoctrine()->getManager();
        $trick = $video->getTrick();

        $trick->removeVideo($video);
        $em->persist($trick);
        $em->flush();

        $this->addFlash('success','La vidéo a été supprimé');

        return $this->redirectToRoute('modify', [
            'trick' => $trick->getId()
        ]);

    }
}
