<?php

namespace App\Controller;

use App\Entity\Picture;
use App\Form\PictureType;
use App\Service\PictureService;
use App\Service\UserService;
use Symfony\Component\HttpFoundation\Request;
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
    public function addPicture(Request $request, PictureService $pictureService)
    {
        $user = $this->getUser();

        if (null != $user->getPicture()) {
            $pictureService->delete($user->getPicture());
        }

        $picture = new Picture();

        $form = $this->createForm(PictureType::class, $picture);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $picture->setUser($user);
            $pictureService->save($picture);

            $this->addFlash('success', 'La photo a été correctement ajoutée');

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
    public function delPicture(PictureService $pictureService, UserService $userService)
    {
        $user = $this->getUser();
        $pictureService->delete($user->getPicture());
        $user->setPicture(null);
        $userService->updateUser($user);

        $this->addFlash('success', 'La photo a été correctement supprimée');

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
