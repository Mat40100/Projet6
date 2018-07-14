<?php

namespace App\Controller;

use App\Entity\Comment;
use App\Entity\Media;
use App\Entity\Trick;
use App\Form\CommentType;
use App\Form\TrickType;
use Doctrine\Bundle\DoctrineBundle\DoctrineBundle;
use Doctrine\DBAL\Types\IntegerType;
use Doctrine\ORM\Mapping\Entity;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;

use Symfony\Component\HttpFoundation\Response;

/**
 * Class TrickController
 * @package App\Controller
 */
class TrickController extends Controller
{

    /**
     * @return \Symfony\Component\HttpFoundation\Response
     * @Route("/", name="home")
     */
    public function index()
    {
        $tricksLeft = true;

        $numberToLoad = 10;

        $tricks = $this->getDoctrine()
            ->getRepository(Trick::class)
            ->findBy([], array('date' => 'desc'));

        $tricksNumber = count($tricks);

        if ($numberToLoad>=$tricksNumber) {
            $numberToLoad = $tricksNumber;
            $tricksLeft = false;
        }

        $tricks = array_slice($tricks,0,$numberToLoad);

        return $this->render('trick/index/index.html.twig', [
            'tricks' => $tricks,
            'number' => 10,
            'tricksLeft' => $tricksLeft
        ]);
    }

    /**
     * @return \Symfony\Component\HttpFoundation\Response
     * @Route("/view/{trick}-{slug}",name="view")
     */
    public function viewTrick(Request $request, Trick $trick)
    {
        $em = $this->getDoctrine()->getManager();
        $comment = new Comment();
        $form = $this->createForm(CommentType::class,$comment);

        if($request->isMethod('POST')){
            $form->handleRequest($request);
            if($form->isSubmitted() && $form->isValid()){
                $comment->setAuthor($this->getUser());
                $trick->addComment($comment);
                $em->persist($trick);
                $em->flush();

                $comment = new Comment();
                $form = $this->createForm(CommentType::class,$comment);
            }
        }

        return $this->render('trick/view.html.twig',[
            "trick" => $trick,
            'form' => isset($form) ? $form->createView() : null,
        ]);
    }

    /**
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     * @Route("/add",name="add")
     * @Security("has_role('ROLE_USER')")
     */
    public function addTrick(Request $request)
    {
        $trick = new Trick();
        $form = $this->createForm(TrickType::class,$trick);
        $repo = $this->getDoctrine()->getRepository(Trick::class);

        if($request->isMethod('POST')){
            $form->handleRequest($request);
            if($form->isValid()){
                if($repo->findOneBy(['name' => $trick->getName()])){
                    $this->addFlash('warning','Ce trick est déjà existant');

                    return $this->render('trick/Forms/addTrickForm.html.twig', [
                        'form' => $form->createView()
                    ]);
                }

                $trick->setDate(new \DateTime());
                $trick->setAuthor($this->getUser());
                $em = $this->getDoctrine()->getManager();
                $em->persist($trick);
                $em->flush();

                $this->addFlash('success','Votre nouveau Trick est bien enregistré!');

                return $this->redirectToRoute('view',array('trick' => $trick->getId(),"slug"=>$trick->getName()));
            }
        }

        return $this->render('trick/Forms/addTrickForm.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/delete/{trick}" ,name="delete")
     * @Security("has_role('ROLE_USER')")
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function deleteTrick(Request $request, Trick $trick)
    {

        $em = $this->getDoctrine()->getManager();

        if (!$trick) {
            $this->addFlash('danger','Vous essayez de supprimer un trick introuvable ...');

            $this->redirectToRoute('home');
        }

        $form = $this->get('form.factory')->create();

        if ($request->isMethod('POST')) {
            $em->remove($trick);
            $em->flush();

            $this->addFlash('success','Le trick a été supprimé!');

            return $this->redirectToRoute('home');
        }

        return $this->render('trick/Forms/deleteForm.html.twig', [
            'form' => $form->createView(),
            'trick' => $trick
        ]);
    }

    /**
     * @param Request $request
     * @param Trick $trick
     * @return Response
     * @Route("modify/{trick}", name="modify")
     */
    public function modifyTrick(Request $request, Trick $trick)
    {
        $em = $this->getDoctrine()->getManager();
        $medias = $trick->getMedias();
        $videos = $trick->getVideos();

        $form= $this->createForm(TrickType::class, $trick);
        $form->remove('medias');
        $form->remove('videos');

        if($request->isMethod('POST')){
            $form->handleRequest($request);
            if($form->isValid()){
                $trick->setDateLastMod(new \DateTime());
                $em->persist($trick);
                $em->flush();

                $this->addFlash('success','Le trick a été modifié!');

                return $this->redirectToRoute('view',array('trick'=>$trick->getId(), 'slug'=> $trick->getName()));
            }
            $this->addFlash('danger','Un problème est survenu pendant l\'enregistrement du trick :(');

            return $this->render('trick/Forms/modTrick.html.twig', array(
                'form' => $form->createView(),
                'videos'=> $videos,
                'medias' => $medias,
                'trick' => $trick
            ));
        }

        return $this->render('trick/Forms/modTrick.html.twig', array(
            'form' => $form->createView(),
            'videos'=> $videos,
            'medias' => $medias,
            'trick' => $trick
        ));
    }

    /**
     * @param Request $request
     * @return bool|Response
     * @Route("loadmore", name="loadmore")
     */
    public function loadMore(Request $request)
    {
        if($request->isXmlHttpRequest()) {
            $numberToLoad = $request->get('tricksNumber');
            $tricksLeft = true;

            $tricks = $this->getDoctrine()
                ->getRepository(Trick::class)
                ->findBy([], array('date' => 'desc'));

            $tricksNumber = count($tricks);
            $numberToLoad = $numberToLoad + 5;

            if ($numberToLoad>=$tricksNumber) {
                $numberToLoad = $tricksNumber;
                $tricksLeft = false;
            }


            $tricks = array_slice($tricks,0,$numberToLoad);

            return $this->render('trick/index/loadmore.html.twig', [
                'tricks' => $tricks,
                'number' => $numberToLoad,
                'tricksLeft' => $tricksLeft
            ]);
    }

        return false;
    }
}
