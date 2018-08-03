<?php

namespace App\Service;


use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class UserService
{
    private $em;
    private $passwordEncoder;
    private $mailer;
    private $twig;


    public function __construct(UserPasswordEncoderInterface $passwordEncoder, EntityManagerInterface $em, \Swift_Mailer $mailer, \Twig_Environment $twig)
    {
        $this->em = $em;
        $this->passwordEncoder = $passwordEncoder;
        $this->mailer = $mailer;
        $this->twig = $twig;
    }

    /**
     * @param User $user
     * @return bool
     */
    public function createUser(User $user)
    {
        $user = $this->EncodePassword($user);
        $user->setRoles('ROLE_USER');

        $this->em->persist($user);
        $this->em->flush();

        return true;
    }

    /**
     * Encode password for user sent in
     * @param User $user
     * @return User
     */
    public function EncodePassword(User $user)
    {
        $password = $this->passwordEncoder->encodePassword($user, $user->getPlainPassword());
        $user->setPassword($password);

        return $user;
    }

    /**
     * @param $user
     * @return bool
     */
    public function updateUser($user)
    {
        $user = $this->EncodePassword($user);
        $this->em->flush();

        return true;
    }
    /**
     * @param EntityRepository $repo
     * @param User $user
     * @return bool
     */
    public function isUserExists(EntityRepository $repo, User $user)
    {
        if($repo->findOneBy(['username'=>$user->getUsername()])!=null ||
            $repo->findOneBy(['email'=>$user->getEmail()])!=null){

            return true;
        }
    }

    /**
     * @param EntityRepository $repo
     * @param User $user
     * @return bool
     * @throws \Twig_Error_Loader
     * @throws \Twig_Error_Runtime
     * @throws \Twig_Error_Syntax
     */
    public function sendRecoveryMail(EntityRepository $repo, User $user)
    {
        $user = $repo->findOneBy(['username' => $user->getUsername()]);

        $token = $user->getRecoveryToken();
        $message =(new \Swift_Message('SnowTricks password recovery'))
            ->setFrom('mathieu.dolhen@gmail.com')
            ->setTo($user->getEmail())
            ->setBody(
                $this->twig->render('registration/RecoveryMail.html.twig',['token' => $token]),
                'text/html'
            );

        $this->mailer->send($message);

        return true;
    }
}