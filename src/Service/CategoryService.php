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
    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    public function getAllCategories()
    {
        return $this->em->getRepository('App:Category')->findAll();
    }
}