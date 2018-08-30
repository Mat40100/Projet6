<?php

namespace App\Controller;

use App\Entity\Category;
use App\Entity\Comment;
use App\Entity\Media;
use App\Entity\MediaVideo;
use App\Entity\Trick;
use App\Form\CategoryType;
use App\Form\CommentType;
use App\Form\MediaType;
use App\Form\MediaVideoType;
use App\Form\ModifyTrickFormType;
use App\Form\TrickType;
use App\Form\TrickTypeModify;
use App\Service\CategoryService;
use App\Service\MediaService;
use App\Service\TrickService;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

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

        if ($numberToLoad >= $tricksNumber) {
            $numberToLoad = $tricksNumber;
            $tricksLeft = false;
        }

        $tricks = array_slice($tricks, 0, $numberToLoad);

        return [
            'tricks' => $tricks,
            'number' => 10,
            'tricksLeft' => $tricksLeft,
        ];
    }

    /**
     * @Template()
     * @Route("/view/{trick}-{slug}")
     */
    public function view(Request $request, Trick $trick)
    {
        $manager = $this->getDoctrine()->getManager();
        $comment = new Comment();
        $form = $this->createForm(CommentType::class, $comment);

        if ($request->isMethod('POST')) {
            $form->handleRequest($request);
            if ($form->isSubmitted() && $form->isValid()) {
                $comment->setAuthor($this->getUser());
                $trick->addComment($comment);
                $manager->persist($trick);
                $manager->flush();

                $comment = new Comment();
                $form = $this->createForm(CommentType::class, $comment);
            }
        }

        return [
            'trick' => $trick,
            'form' => isset($form) ? $form->createView() : null,
        ];
    }

    /**
     * @Template()
     * @Route("/add")
     * @Security("has_role('ROLE_USER')")
     */
    public function add(Request $request, TrickService $trickService)
    {
        $trick = new Trick();

        $form = $this->createForm(TrickType::class, $trick);
        $repo = $this->getDoctrine()->getRepository(Trick::class);


        if ($request->isMethod('POST')) {
            $form->handleRequest($request);

            if ($form->isValid()) {
                if ($trickService->isExists($repo, $trick)) {
                    $this->addFlash('warning', 'Ce trick '.$trick->getName().' est déjà existant');

                    return array(
                        'form' => $form->createView(),
                    );
                }

                if ($trickService->add($this->getUser(), $trick)) {
                    $this->addFlash('success', 'Votre nouveau Trick '.$trick->getName().' est bien enregistré!');

                    return $this->redirectToRoute(
                        'app_trick_view',
                        array('trick' => $trick->getId(), 'slug' => $trick->getName())
                    );
                }
            }
        }

        return [
            'form' => $form->createView(),
        ];
    }

    /**
     * @Route("/delete/{trick}")
     * @Security("has_role('ROLE_USER')")
     * @Template()
     */
    public function delete(Request $request, Trick $trick, TrickService $trickServices)
    {
        if (!$trick) {
            $this->addFlash('danger', 'Vous essayez de supprimer un trick introuvable ...');

            $this->redirectToRoute('home');
        }

        $form = $this->get('form.factory')->create();

        if ($request->isMethod('POST')) {
            $trickServices->remove($trick);
            $this->addFlash('success', $trick->getName().' a été supprimé!');

            return $this->redirectToRoute('app_trick_index');
        }

        return [
            'form' => $form->createView(),
            'trick' => $trick,
        ];
    }

    /**
     * @Route("modify/{trick}")
     * @Security("has_role('ROLE_USER')")
     * @Template()
     */
    public function modify(Request $request, Trick $trick, TrickService $trickService)
    {
        $form = $this->createForm(TrickType::class, $trick);

        if ($request->isMethod('POST')) {
            $form->handleRequest($request);

            if ($form->isValid()) {
                if ($trickService->update($trick)) {
                    $this->addFlash('success', 'La modification de ' . $trick->getName() . ' est bien enregistrée!');

                    return $this->redirectToRoute(
                       'app_trick_view',
                       array('trick' => $trick->getId(), 'slug' => $trick->getName())
                   );
                }
            }
        }

        return [
            'form' => $form->createView(),
            'trick' => $trick,
        ];
    }

    /**
     * @Route("/thisIsMainMedia/{media}")
     * @Security("has_role('ROLE_USER')")
     */
    public function thisIsMainMedia(Media $media, TrickService $trickService)
    {
        $trick = $media->getTrick();

        $trick ->setMainMedia($media);

        if ($trickService->update($trick)) {
            $this->addFlash('success', 'Modification enregistrée !');
        }

        return $this->redirectToRoute('app_trick_modify', [
            'trick' => $trick->getId(),
        ]);
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

        if ($numberToLoad >= $tricksNumber-1) {
            $numberToLoad = $tricksNumber;
            $tricksLeft = false;
        }

        $tricks = array_slice($tricks, 0, $numberToLoad);

        return [
            'tricks' => $tricks,
            'loaded' => $numberToLoad,
            'tricksLeft' => $tricksLeft,
        ];
    }
}
