<?php

namespace Govnokod\CodeBundle\Service;

use Doctrine\Common\Persistence\ObjectManager;

class CategoryService
{
    /**
     * @var ObjectManager
     */
    private $entityManager;

    public function __construct(ObjectManager $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function getEntity($category)
    {
        $categoryRepository = $this->entityManager->getRepository("GovnokodCodeBundle:Category");
        $entity = $categoryRepository->findOneByName($category);

        return $entity;
    }

}
