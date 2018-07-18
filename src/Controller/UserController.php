<?php

namespace App\Controller;

use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

class UserController extends Controller
{
    /**
     * @Route("/account")
     * @Template("user/index.html.twig")
     */
    public function account()
    {
        $user = $this->getUser();

        return [
            'user' => $user,
        ];
    }
}
