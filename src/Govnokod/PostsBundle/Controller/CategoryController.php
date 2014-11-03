<?php

namespace Govnokod\PostsBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class CategoryController extends Controller
{
    /**
     * @todo: wiistriker: избавиться от тяжелого запроса в пользу денормализованного поля
     */
    public function listAction()
    {
        $repository = $this->getDoctrine()->getRepository('GovnokodPostsBundle:Category');
        /* @var $qb \Doctrine\ORM\QueryBuilder */
        $qb = $repository->createQueryBuilder('t');
        $query = $qb->select('t.title, t.name, count(c.id) as codes')
                        ->leftJoin('Govnokod\PostsBundle\Entity\Code', 'c', 'WITH', 't.id = c.category')
                        ->orderBy('codes', 'DESC')
                        ->groupBy('t.id')
                        ->getQuery();

        $categories = $query->getResult();

        return $this->render('GovnokodPostsBundle:Category:list.html.twig', array(
            'categories' => $categories
        ));
    }
}
