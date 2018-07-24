<?php

namespace App\Controller;

use App\Entity\Picture;
use App\Form\PictureType;
use App\Form\UserType;
use App\Services\PictureServices;
use App\Services\UserServices;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;


class UserController extends Controller
{
    /**
     * @Route("/account/addPicture")
     * @Template()
     * @Security("has_role('ROLE_USER')")
     */
    public function addPicture(Request $request, UserServices $userServices, PictureServices $pictureServices)
    {
        $user = $this->getUser();

        $picture = new Picture();

        $form = $this->createForm(PictureType::class, $picture);


        $form->handleRequest($request);

        if ($form -> isSubmitted() && $form ->isValid() ) {
            $picture->setUser($user);
            $pictureServices->save($picture);

            $this->addFlash('success', "La photo a été correctement ajoutée");

            return $this->redirectToRoute('app_user_index');
        }

        return [
            'user' => $user,
            'form' => $form->createView(),
        ];
    }

    /**
     * @Route("/account/delPicture")
     * @Security("has_role('ROLE_USER')")
     */
    public function delPicture(PictureServices $pictureServices, UserServices $userServices)
    {
        $user = $this->getUser();
        $pictureServices->delete($user->getPicture());
        $user->setPicture(null);
        $userServices->updateUser($user);

        $this->addFlash('success', "La photo a été correctement supprimée");

        return $this->redirectToRoute('app_user_index');
    }

    /**
     * @Route("/account")
     * @Template()
     * @Security("has_role('ROLE_USER')")
     */
    public function index()
    {
        $user = $this->getUser();

        return [
          'user' => $user,
        ];
    }
}
