<?php

namespace App\Controller;

use App\Entity\Comment;
use App\Entity\Media;
use App\Entity\Trick;
use App\Form\CommentType;
use App\Form\TrickType;
use App\Services\TrickServices;
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

class TrickController extends Controller
{
    /**
     * @Template()
     * @Route("/")
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

        return [
            'tricks' => $tricks,
            'number' => 10,
            'tricksLeft' => $tricksLeft
        ];
    }

    /**
     * @Template()
     * @Route("/view/{trick}-{slug}")
     */
    public function view(Request $request, Trick $trick)
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

        return [
            "trick" => $trick,
            'form' => isset($form) ? $form->createView() : null,
        ];
    }

    /**
     * @Template()
     * @Route("/add")
     * @Security("has_role('ROLE_USER')")
     */
    public function add(Request $request, TrickServices $trickServices)
    {
        $trick = new Trick();
        $form = $this->createForm(TrickType::class,$trick);
        $repo = $this->getDoctrine()->getRepository(Trick::class);

        if($request->isMethod('POST')){

            $form->handleRequest($request);

            if($form->isValid()){

                if($trickServices->isExists($repo, $trick)){

                    $this->addFlash('warning','Ce trick est déjà existant');

                    return array(
                        'form' => $form->createView()
                    );
                }

                if ($trickServices->add($this->getUser(),$trick)) {

                    $this->addFlash('success','Votre nouveau Trick est bien enregistré!');

                    return $this->redirectToRoute(
                        'app_trick_view',
                        array('trick' => $trick->getId(),"slug"=>$trick->getName()));
                }
             }
        }

        return [
            'form' => $form->createView()
        ];
    }

    /**
     * @Route("/delete/{trick}")
     * @Security("has_role('ROLE_USER')")
     * @Template()
     */
    public function delete(Request $request, Trick $trick, TrickServices $trickServices)
    {
        if (!$trick) {
            $this->addFlash('danger','Vous essayez de supprimer un trick introuvable ...');

            $this->redirectToRoute('home');
        }

        $form = $this->get('form.factory')->create();

        if ($request->isMethod('POST')) {

            $trickServices->remove($trick);
            $this->addFlash('success','Le trick a été supprimé!');

            return $this->redirectToRoute('app_trick_index');
        }

        return [
            'form' => $form->createView(),
            'trick' => $trick
        ];
    }

    /**
     * @Route("modify/{trick}")
     * @Template()
     */
    public function modify(Request $request, Trick $trick, TrickServices $trickServices)
    {
        $form= $this->createForm(TrickType::class, $trick);
        $form->remove('medias');
        //$form->remove('videos');

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            $this->addFlash('success','Le trick a été modifié!');

            $trickServices->update($trick);

            return $this->redirectToRoute(
                'app_trick_view',
                array('trick'=>$trick->getId(), 'slug'=> $trick->getName())
            );
        }


        return [
            'form' => $form->createView(),
            'trick' => $trick
        ];
    }

    /**
     * @Route("loadMore")
     * @Template()
     */
    public function loadMore(Request $request)
    {
        if (!$request->isXmlHttpRequest()) {
            return false;
        }

        $numberToLoad = $request->get('tricksNumber');
        $tricksLeft = true;

        $tricks = $this->getDoctrine()
            ->getRepository(Trick::class)
            ->findBy([], array('date' => 'desc'));

        $tricksNumber = count($tricks);
        $numberToLoad = $numberToLoad + 5;

        if ($numberToLoad >= $tricksNumber) {
            $numberToLoad = $tricksNumber;
            $tricksLeft = false;
        }


        $tricks = array_slice($tricks, 0, $numberToLoad);

        return [
            'tricks' => $tricks,
            'number' => $numberToLoad,
            'tricksLeft' => $tricksLeft
        ];
    }
}
