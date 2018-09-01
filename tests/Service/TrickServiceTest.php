<?php
/**
 * Created by PhpStorm.
 * User: Mathieu
 * Date: 31/08/2018
 * Time: 20:17
 */

namespace App\Tests\Service;


use App\Entity\Trick;
use App\Service\TrickService;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use PHPUnit\Framework\TestCase;

class TrickServiceTest extends TestCase
{
    public function testIsExists()
    {
        $trick = new Trick();
        $trick->setName('180');
        $falseTrick = new Trick();

        $entityManager = $this->createMock(EntityManagerInterface::class);

        $repo = $this->createMock(EntityRepository::class);

        $repo->method('findOneBy')
            ->will($this->onConsecutiveCalls($falseTrick, null))
        ;

        $trickService = new TrickService($entityManager);

        $this->assertSame(true , $trickService->isExists($repo, $trick));
        $this->assertSame(false , $trickService->isExists($repo, $trick));

    }
}