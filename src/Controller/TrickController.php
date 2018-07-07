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
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\HttpKernel\Exception\HttpException;
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
     * @param $id
     * @return \Symfony\Component\HttpFoundation\Response
     * @Route("/view/{trick}-{slug}",name="view")
     */
    public function viewTrick(Trick $trick, AuthorizationCheckerInterface $authChecker)
    {

        $author = $trick->getAuthor();
        $comments = $trick->getComments();
        $medias = $trick->getMedias();
        $videos = $trick->getVideos();


        if ($authChecker->isGranted('ROLE_USER')){
            $comment = new Comment();
            $form = $this->createForm(CommentType::class,$comment, array(
                'action' => $this->generateUrl('addcomment'),
            ));
            $form->add('id',HiddenType::class, array('data'=>$trick->getId(), 'mapped' => false));

            return $this->render('trick/view.html.twig',[
                "trick" => $trick,
                "author" => $author,
                "comments" => $comments,
                "medias" => $medias,
                'videos' => $videos,
                'form' => $form->createView()
            ]);
        }

        return $this->render('trick/view.html.twig',[
            "trick" => $trick,
            "author" => $author,
            "comments" => $comments,
            "medias" => $medias,
            'videos' => $videos
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


        if($request->isMethod('POST')){
            $form->handleRequest($request);
            if($form->isValid()){
                foreach($form['medias']->getData() as $key => $value){
                    if($value->getFile() === null ){
                        $this->addFlash('notice','L\'image n\'etait pas au bon format, et n\'a pas été enregistrée');
                        $trick->removeMedia($value);
                    }
                }
                foreach($form['videos']->getData() as $key => $value){
                    if($value->getUrl() === null ){
                        $this->addFlash('notice','La vidéo n\'etait pas au bon format, et n\'a pas été enregistrée');
                        $trick->removeVideo($value);
                    }
                }
                $trick->setDate(new \DateTime());
                $trick->setAuthor($this->getUser());
                $em = $this->getDoctrine()->getManager();
                $em->persist($trick);
                $em->flush();

                $this->addFlash('notice','Votre nouveau Trick est bien enregistré!');

                return $this->redirectToRoute('view',array('trick' => $trick->getId(),"slug"=>$trick->getName()));
            }
        }
        return $this->render('trick/Forms/addTrickForm.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * @param Request $request
     * @Route("/delete/{trick}" ,name="delete")
     * @Security("has_role('ROLE_USER')")
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function deleteTrick(Request $request, Trick $trick)
    {

        $em = $this->getDoctrine()->getManager();

        if (!$trick) {
            throw $this->createNotFoundException("No Trick found for this id : " . $trick->getId());
        }

        $form = $this->get('form.factory')->create();

        if ($request->isMethod('POST')) {
            $em->remove($trick);
            $em->flush();

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

                return $this->redirectToRoute('view',array('trick'=>$trick->getId(), 'slug'=> $trick->getName()));
            }
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
