<?php

namespace App\Controller;

use App\Entity\User;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;


class SecurityController extends Controller
{
    /**
     * @Route("/login", name="login")
     */
    public function login(Request $request, AuthenticationUtils $authenticationUtils)
    {
        $error = $authenticationUtils->getLastAuthenticationError();
        $lastUserName = $authenticationUtils->getLastUsername();

        return $this->render('Security/login.html.twig', array([
            'lastUserName' => $lastUserName,
            'error' => $error
        ]));
    }

    /**
     * @Route("/logout", name="logout")
     */
    public function logout(){
        $this->addFlash('success','Vous êtes maintenant déconnecté !');

        $this->redirectToRoute('home');
    }
}
