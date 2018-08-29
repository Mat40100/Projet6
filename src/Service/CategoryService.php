<?php
/**
 * Created by PhpStorm.
 * User: Mathieu
 * Date: 21/08/2018
 * Time: 17:39
 */

namespace App\Service;

use Doctrine\ORM\EntityManagerInterface;

class CategoryService
{
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function getAllCategories()
    {
        return $this->entityManager->getRepository('App:Category')->findAll();
    }
}
