<?php

namespace App\Service;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class UserService
{
    private $entityManager;
    private $passwordEncoder;
    private $mailer;
    private $twig;

    public function __construct(UserPasswordEncoderInterface $passwordEncoder, EntityManagerInterface $entityManager, \Swift_Mailer $mailer, \Twig_Environment $twig)
    {
        $this->entityManager = $entityManager;
        $this->passwordEncoder = $passwordEncoder;
        $this->mailer = $mailer;
        $this->twig = $twig;
    }

    /**
     * @param User $user
     *
     * @return bool
     */
    public function createUser(User $user)
    {
        $user = $this->encodePassword($this->passwordEncoder, $user);
        $user->setRoles('ROLE_USER');

        $this->entityManager->persist($user);
        $this->entityManager->flush();

        return true;
    }

    /**
     * @param UserPasswordEncoderInterface $passwordEncoder
     * @param User $user
     * @return User
     */
    public function encodePassword(UserPasswordEncoderInterface $passwordEncoder, User $user)
    {
        $password = $passwordEncoder->encodePassword($user, $user->getPlainPassword());
        $user->setPassword($password);

        return $user;
    }

    /**
     * @param $user
     *
     * @return bool
     */
    public function updateUser($user)
    {
        $user = $this->encodePassword($this->passwordEncoder,$user);
        $this->entityManager->flush();

        return true;
    }

    /**
     * @param EntityRepository $repo
     * @param User $user
     * @return bool
     */
    public function isUserExists(EntityRepository $repo, User $user)
    {
        $bool = $repo->findOneBy(['username' => $user->getUsername()]) ?: null;

        if (null === $bool) {
            $bool = $repo->findOneBy(['email' => $user->getEmail()]) ?: null;
        }

        if (null === $bool) {

            return false;
        }

        return true;
    }

    /**
     * @param EntityRepository $repo
     * @param User             $user
     *
     * @return bool
     *
     * @throws \Twig_Error_Loader
     * @throws \Twig_Error_Runtime
     * @throws \Twig_Error_Syntax
     */
    public function sendRecoveryMail(User $user)
    {
        $token = $user->getRecoveryToken();
        $message = (new \Swift_Message('SnowTricks password recovery'))
            ->setFrom('mathieu.dolhen@gmail.com')
            ->setTo($user->getEmail())
            ->setBody(
                $this->twig->render('registration/RecoveryMail.html.twig', ['token' => $token, 'user' => $user]),
                'text/html'
            );

        $this->mailer->send($message);

        return true;
    }

    /**
     * @return EntityManagerInterface
     */
    public function getEntityManager(): EntityManagerInterface
    {
        return $this->entityManager;
    }

    /**
     * @param EntityManagerInterface $entityManager
     */
    public function setEntityManager(EntityManagerInterface $entityManager): void
    {
        $this->entityManager = $entityManager;
    }

    /**
     * @return UserPasswordEncoderInterface
     */
    public function getPasswordEncoder(): UserPasswordEncoderInterface
    {
        return $this->passwordEncoder;
    }

    /**
     * @param UserPasswordEncoderInterface $passwordEncoder
     */
    public function setPasswordEncoder($passwordEncoder): void
    {
        $this->passwordEncoder = $passwordEncoder;
    }

    /**
     * @return \Swift_Mailer
     */
    public function getMailer(): \Swift_Mailer
    {
        return $this->mailer;
    }

    /**
     * @param \Swift_Mailer $mailer
     */
    public function setMailer(\Swift_Mailer $mailer): void
    {
        $this->mailer = $mailer;
    }

    /**
     * @return \Twig_Environment
     */
    public function getTwig(): \Twig_Environment
    {
        return $this->twig;
    }

    /**
     * @param \Twig_Environment $twig
     */
    public function setTwig(\Twig_Environment $twig): void
    {
        $this->twig = $twig;
    }


}
