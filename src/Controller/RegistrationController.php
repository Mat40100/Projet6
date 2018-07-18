<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\NewPasswordType;
use App\Form\RecoveryType;
use App\Form\UserType;
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
    public function register(Request $request, UserPasswordEncoderInterface $passwordEncoder)
    {
        $user = new User();
        $form = $this->createForm(UserType::class, $user);

        if ($request->isMethod('POST')) {
            $form->handleRequest($request);
            if($form->isValid()){
                $repo = $this->getDoctrine()->getRepository(User::class);
                if($repo->findOneBy(['username'=>$user->getUsername()])!=null || $repo->findOneBy(['email'=>$user->getEmail()])!=null){
                    $this->addFlash('warning','Mail ou Utilisateur déjà existant');

                    return [
                        'form' => $form->createView()
                    ];
                }
                $password = $passwordEncoder->encodePassword($user, $user->getPlainPassword());
                $user->setPassword($password);
                $user->setRoles('ROLE_USER');

                $em = $this->getDoctrine()->getManager();
                $em->persist($user);
                $em->flush();

                $this->addFlash('success','Votre compte a été enregistré!');

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
    public function passwordForgotten(Request $request, \Swift_Mailer $mailer)
    {
        $form = $this->createForm(RecoveryType::class);
        $em = $this->getDoctrine()->getRepository(User::class);

        if($request->isMethod('POST')){
            $form->handleRequest($request);
            if($form->isValid()){
                if ($em->findOneBy(['username'=>$form->getData()['username']]) === null){
                    $this->addFlash('warning','This username doesn\'t exists');

                    return [
                        'form'=>$form->createView()
                    ];
                }
                $username =  $form->getData()['username'];
                $user = $em->findOneBy(['username' => $username]);

                $token = $user->getRecoveryToken();
                $message =(new \Swift_Message('SnowTricks password recovery'))
                    ->setFrom('mathieu.dolhen@gmail.com')
                    ->setTo($user->getEmail())
                    ->setBody(
                        $this->renderView(
                            'registration/RecoveryMail.html.twig',array(
                                'token' => $token
                            )
                        ),'text/html'
                    );

                $mailer->send($message);

                $this->addFlash('success','Un e-mail pour creer un nouveau mot de passe a été envoyé !');

                return $this->redirectToRoute('app_trick_index');
            }
        }

        return [
            'form'=>$form->createView()
        ];
    }

    /**
     * @Template("registration/password_forgotten.html.twig")
     * @Route("token/{token}", requirements={"token"="[a-zA-Z0-9]*"})
     * @ParamConverter("user", options={"mapping": {"recovery_token" : "token"} })
     */
    public function tokenRecovery(Request $request, User $user, UserPasswordEncoderInterface $passwordEncoder)
    {
        $em = $this->getDoctrine()->getManager();
        $form = $this->createForm(NewPasswordType::class);

        if($request->isMethod('POST')){
            $form->handleRequest($request);
            if($form->isValid()){
                $password = $passwordEncoder->encodePassword($user, $form->get('plainPassword')->getData());
                $user->setPassword($password);
                $user->generateToken();

                $em->flush();

                $this->addFlash('success','Le mot de passe à été réinitialisé');

                return $this->redirectToRoute('app_trick_index');
            }
            $this->addFlash('warning','Les mots de passe doivent correspondre');
        }

        return [
            'form'=>$form->createView()
        ];
    }

}
