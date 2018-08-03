<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\NewPasswordType;
use App\Form\RecoveryType;
use App\Form\UserType;
use App\Service\UserService;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;


class RegistrationController extends Controller
{
    /**
     * @Template()
     * @Route("/register")
     */
    public function register(Request $request, UserService $userService)
    {
        $user = new User();
        $form = $this->createForm(UserType::class, $user);

        if ($request->isMethod('POST')) {
            $form->handleRequest($request);

            if($form->isValid()){
                $repo = $this->getDoctrine()->getRepository(User::class);

                if($userService->isUserExists($repo, $user)){
                    $this->addFlash('warning','Mail ou Utilisateur déjà existant');

                    return [
                        'form' => $form->createView()
                    ];
                }

                if ($userService->createUser($user)){
                    $this->addFlash('success','Votre compte a été enregistré avec succès!');
                };

                return $this->redirectToRoute('app_trick_index');
            }
        }

        return [
            'form' => $form->createView()
        ];
    }

    /**
     * @Template()
     * @Route("/recovery")
     */
    public function passwordForgotten(Request $request, \Swift_Mailer $mailer, UserService $userService)
    {
        $form = $this->createForm(RecoveryType::class);
        $repo = $this->getDoctrine()->getRepository(User::class);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()){
            $user = new User;
            $user->setUsername($form->getData()['username']);

            if (!$userService->isUserExists($repo, $user)){
                $this->addFlash('warning','This username doesn\'t exists');

                return [
                    'form'=>$form->createView()
                ];
            }

            if ($userService->sendRecoveryMail($repo, $user)){
                $this->addFlash('success','Un e-mail pour creer un nouveau mot de passe a été envoyé !');
            }

            return $this->redirectToRoute('app_trick_index');
        }

        return [
            'form'=>$form->createView()
        ];
    }

    /**
     * @Template("registration/password_forgotten.html.twig")
     * @Route("token/{token}", requirements={"token"="[a-zA-Z0-9]*"})
     * @ParamConverter("user", options={"mapping": {"token": "recoveryToken"}})
     */
    public function tokenRecovery(Request $request, User $user, UserService $userService)
    {
        $form = $this->createForm(NewPasswordType::class);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
            $user->setPlainPassword($form->get('plainPassword')->getData());

            if ($userService->updateUser($user)) {
                $this->addFlash('success','Le mot de passe à été réinitialisé');
            }

            return $this->redirectToRoute('app_trick_index');
        }

        $this->addFlash('warning','Les mots de passe doivent correspondre');

        return [
            'form'=>$form->createView()
        ];
    }
}
