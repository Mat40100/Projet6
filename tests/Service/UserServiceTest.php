<?php
/**
 * Created by PhpStorm.
 * User: Mathieu
 * Date: 01/09/2018
 * Time: 13:21
 */

namespace App\Tests\Service;
use App\Entity\User;
use Doctrine\ORM\EntityRepository;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;


class UserServiceTest extends WebTestCase
{

    public function testEncodePassword()
    {
        self::bootKernel();
        $container = self::$container;

        $user = new User();
        $encodeInterface = $container->get('security.password_encoder');
        $userService = $container->get('test.userServices');

        $user->setPlainPassword('Test');

        $user = $userService->encodePassword($encodeInterface, $user);

        $this->assertTrue($encodeInterface->isPasswordValid($user, $user->getPlainPassword()));
    }

    public function testIsUserExists()
    {
        self::bootKernel();
        $container = self::$container;
        $userService = $container->get('test.userServices');

        $falseUser = new User();
        $user = New User();


        $repo = $this->createMock(EntityRepository::class);
        $repo
            ->method('findOneBy')
            ->will(self::onConsecutiveCalls(null , $falseUser , null , null, $falseUser, null))
        ;

        $this->assertEquals(true, $userService->isUserExists($repo, $user)); // $user exists
        $this->assertEquals(false, $userService->isUserExists($repo, $user)); // $user not exists
        $this->assertEquals(true, $userService->isUserExists($repo, $user)); // $user exists
    }
}