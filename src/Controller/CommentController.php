<?php

namespace App\Controller;

use App\Entity\Comment;
use App\Entity\Trick;
use App\Form\CommentType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\Form\FormTypeInterface;
class CommentController extends Controller
{
    /**
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     * @Route("/addComment", name="addcomment")
     * @Security("has_role('ROLE_USER')")
     */
    public function addComment(Request $request){

        $id = $request->request->get('comment')['id'];

        $trick = $this->getDoctrine()
            ->getRepository(Trick::class)
            ->find($id);

        if(!$trick){
            $this->addFlash('warning','Le trick pour lequel vous voulez poster un commentaire est introuvable');

            return $this->redirectToRoute('home');
        }

        $comment = new Comment();
        $form = $this->createForm(CommentType::class,$comment)
            ->add('id',HiddenType::class, array('mapped'=> false));

        if($request->isMethod('POST')){
            $form->handleRequest($request);
            if($form->isValid()) {
                if(empty($comment->getContent())){
                    $this->addFlash('warning','Vous devez mettre un contenu dans votre commentaire');

                    return $this->redirectToRoute('view', array('trick' => $trick->getId(),"slug"=>$trick->getName()));
                }
                $em = $this->getDoctrine()->getManager();
                $comment->setDate(new \DateTime());
                $comment->setAuthor($this->getUser());

                $trick->addComment($comment);
                $em->persist($trick);
                $em->flush();

                return $this->redirectToRoute('view', array('trick' => $trick->getId(),"slug"=>$trick->getName()));
            }
        }

        $this->addFlash('warning','Le formulaire n\'est pas correctement rempli');

        return $this->redirectToRoute('view', array('trick' => $trick->getId(),"slug"=>$trick->getName()));
    }
}
