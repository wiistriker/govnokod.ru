<?php

namespace Govnokod\CodeBundle\Service;

use Doctrine\Common\Persistence\ObjectManager;

class CodeService
{
    /**
     * @var ObjectManager
     */
    private $entityManager;

    public function __construct(ObjectManager $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function paginate($page, $pageSize, $categoryEntity)
    {
        /* @var $codeRepository \Doctrine\ORM\EntityRepository */
        $codeRepository = $this->entityManager->getRepository('GovnokodCodeBundle:Code');

        $countQuery = $codeRepository->createQueryBuilder('c')
            ->select('count(c.id) as _count');

        if ($categoryEntity) {
            $countQuery->where('c.category = :category')
                ->setParameter('category', $categoryEntity);
        }

        $count = $countQuery->getQuery()->execute()[0]['_count'];
        $pageCount = (int) ceil($count / $pageSize);

        if ($page < 1 || $page > $pageCount) {
            $page = $pageCount;
        }

        $itemQuery = $codeRepository->createQueryBuilder('c')
            ->select(['c', 'cat'])
            ->join('c.category', 'cat')
            ->orderBy('c.created_at', 'DESC')
            ->setFirstResult(($pageCount - $page) * $pageSize)
            ->setMaxResults($pageSize);

        if ($categoryEntity) {
            $itemQuery->where('c.category = :category')
                ->setParameter('category', $categoryEntity);
        }

        $codes = $itemQuery->getQuery()->execute();

        return [$codes, $pageCount, $page];
    }
}
