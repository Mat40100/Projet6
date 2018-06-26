<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\RecoveryType;
use App\Form\UserType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;


class RegistrationController extends Controller
{
    /**
     * @Route("/register", name="register")
     * @throws \Exception
     */
    public function register(Request $request, UserPasswordEncoderInterface $passwordEncoder)
    {
        $user = new User();
        $form = $this->createForm(UserType::class, $user);

        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()){

            $password = $passwordEncoder->encodePassword($user, $user->getPlainPassword());
            $user->setPassword($password);
            $user->setRoles('ROLE_USER');
            $randomNum = random_bytes(10);
            $user->setRecoveryToken(hash('sha256',$randomNum));

            $em = $this->getDoctrine()->getManager();
            $em->persist($user);
            $em->flush();

            return $this->redirectToRoute('home');
        }

        return $this->render(
            'registration/register.html.twig', array(
                'form' => $form->createView()
            )
        );
    }

    /**
     * @param Request $request
     * @param \Swift_Mailer $mailer
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     * @Route("/recovery", name="recovery")
     */
    public function passwordRecovery(Request $request, \Swift_Mailer $mailer)
    {
        $form = $this->createForm(RecoveryType::class);
        $em = $this->getDoctrine()->getRepository(User::class);

        if($request->isMethod('POST')){
            $form->handleRequest($request);
            if($form->isValid()){
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

                return $this->redirectToRoute('home');
            }
        }
        return $this->render('registration/forgotten.html.twig',array('form'=>$form->createView()));
    }
}
