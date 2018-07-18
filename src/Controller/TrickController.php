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
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
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
     * @return array
     * @Template("base.html.twig")
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

        return array(
            'tricks' => $tricks,
            'number' => 10,
            'tricksLeft' => $tricksLeft
        );
    }

    /**
     * @param Request $request
     * @param Trick $trick
     * @return array
     * @Template("trick/view.html.twig")
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

        return array(
            "trick" => $trick,
            'form' => isset($form) ? $form->createView() : null,
        );
    }

    /**
     * @param Request $request
     * @Template("'trick/Forms/addTrickForm.html.twig")
     * @Route("/add",name="add")
     * @Security("has_role('ROLE_USER')")
     * @return array|\Symfony\Component\HttpFoundation\RedirectResponse|Response
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

                    return array(
                        'form' => $form->createView()
                    );
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

        return array(
            'form' => $form->createView()
        );
    }

    /**
     * @param Request $request
     * @param Trick $trick
     * @return array|\Symfony\Component\HttpFoundation\RedirectResponse
     * @Route("/delete/{trick}" ,name="delete")
     * @Security("has_role('ROLE_USER')")
     * @Template('trick/Forms/deleteTrickForm.html.twig)
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

        return array(
            'form' => $form->createView(),
            'trick' => $trick
        );
    }

    /**
     * @param Request $request
     * @param Trick $trick
     * @return array|\Symfony\Component\HttpFoundation\RedirectResponse|Response
     * @Route("modify/{trick}", name="modify")
     * @Template("trick/forms/modTrick.html.twig")
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

        return array(
            'form' => $form->createView(),
            'videos'=> $videos,
            'medias' => $medias,
            'trick' => $trick
        );
    }

    /**
     * @param Request $request
     * @return bool|array
     * @Route("loadore", name="loadMore")
     * @Template("trick/index/loadMore.html.twig")
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

            return array(
                'tricks' => $tricks,
                'number' => $numberToLoad,
                'tricksLeft' => $tricksLeft
            );
    }

        return false;
    }
}
