<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

class SecurityController extends Controller
{
    /**
     * @Route("/login")
     * @Template()
     */
    public function login(Request $request, AuthenticationUtils $authenticationUtils)
    {
        $error = $authenticationUtils->getLastAuthenticationError();
        $lastUserName = $authenticationUtils->getLastUsername();

        return [
            'lastUserName' => $lastUserName,
            'error' => $error,
        ];
    }

    /**
     * @Route("/logout")
     */
    public function logout()
    {
        $this->addFlash('success', 'Vous êtes maintenant déconnecté !');

        $this->redirectToRoute('app_trick_index');
    }
}
