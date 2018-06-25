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

        if(gettype($id)=='integer'){
            throw $this->createNotFoundException("id must be an integer");
        }

        $trick = $this->getDoctrine()
            ->getRepository(Trick::class)
            ->find($id);

        if(!$trick){
            throw $this->createNotFoundException("No Trick found for this id : ".$id);
        }

        $comment = new Comment();
        $form = $this->createForm(CommentType::class,$comment)
            ->add('id',HiddenType::class, array('mapped'=> false));

        if($request->isMethod('POST')){
            $form->handleRequest($request);
            if($form->isValid()) {
                $em = $this->getDoctrine()->getManager();
                $comment->setDate(new \DateTime());
                $comment->setAuthor($this->getUser());
                $comment->setTrick($trick);

                $em->persist($comment);
                $em->flush();

                return $this->redirectToRoute('view', array('trick' => $trick->getId(),"slug"=>$trick->getName()));
            }
        }

        throw $this->createNotFoundException("Problem while registering your comment");
    }
}
