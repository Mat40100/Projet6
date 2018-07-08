<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\NewPasswordType;
use App\Form\RecoveryType;
use App\Form\UserType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;
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
        $repo = $this->getDoctrine()->getRepository(User::class);

        if($request->isMethod('POST')){
            $form->handleRequest($request);
            if($form->isValid()){
                if($repo->findOneBy(['username'=>$user->getUsername()])!=null || $repo->findOneBy(['email'=>$user->getEmail()])!=null){
                    $this->addFlash('warning','Mail ou Utilisateur déjà existant');

                    return $this->render(
                        'registration/register.html.twig', array(
                            'form' => $form->createView()
                        )
                    );
                }
                $password = $passwordEncoder->encodePassword($user, $user->getPlainPassword());
                $user->setPassword($password);
                $user->setRoles('ROLE_USER');
                $randomNum = random_bytes(10);
                $user->setRecoveryToken(hash('sha256',$randomNum));

                $em = $this->getDoctrine()->getManager();
                $em->persist($user);
                $em->flush();

                $this->addFlash('success','Votre compte a été enregistrer!');

                return $this->redirectToRoute('home');
            }
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
                if($em->findOneBy(['username'=>$form->getData()['username']])==null){
                    $this->addFlash('warning','This username doesn\'t exists');

                    return $this->render('registration/forgotten.html.twig',array('form'=>$form->createView()));
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
                return $this->redirectToRoute('home');
            }
        }
        return $this->render('registration/forgotten.html.twig',array('form'=>$form->createView()));
    }

    /**
     * @param $token
     * @param Request $request
     * @param UserPasswordEncoderInterface $passwordEncoder
     * @return Response
     * @throws \Exception
     * @Route("token/{token}", name="recoveryToken", requirements={"token"="[a-zA-Z0-9]*"})
     */
    public function tokenRecovery($token, Request $request, UserPasswordEncoderInterface $passwordEncoder)
    {
        $em = $this->getDoctrine()->getManager();
        $user = new User();
        $form = $this->createForm(NewPasswordType::class,$user);

        if($request->isMethod('POST')){
            $form->handleRequest($request);
            if($form->isValid()){
                $userToUpdate = $em->getRepository(User::class)
                    ->findOneBy(['recoveryToken' => $user->getRecoveryToken()]);

                $password = $passwordEncoder->encodePassword($user, $user->getPlainPassword());
                $userToUpdate->setPassword($password);

                $randomNum = random_bytes(10);
                $userToUpdate->setRecoveryToken(hash('sha256',$randomNum));

                $em->persist($userToUpdate);
                $em->flush();

                $this->addFlash('success','Le mot de passe à été réinitialisé');

                return $this->redirectToRoute('home');
            }
            $this->addFlash('warning','Les mots de passe doivent correspondre');
        }

        $user = $em->getRepository(User::class)
            ->findOneBy(['recoveryToken' => $token]);

        if($user == null ){
            $this->addFlash('danger','Ce token n\'éxiste pas');

            return $this->redirectToRoute('home');
        }

        $form = $this->createForm(NewPasswordType::class, $user);

        return $this->render('registration/forgotten.html.twig',array('form'=>$form->createView()));
    }

}
