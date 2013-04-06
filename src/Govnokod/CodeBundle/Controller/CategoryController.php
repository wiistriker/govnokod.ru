<?php

namespace Govnokod\CodeBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class CategoryController extends Controller
{
    /**
     * @todo: wiistriker: избавиться от тяжелого запроса в пользу денормализованного поля
     */
	public function listAction()
	{
		$repository = $this->getDoctrine()->getRepository('GovnokodCodeBundle:Category');
		/* @var $qb \Doctrine\ORM\QueryBuilder */
		$qb = $repository->createQueryBuilder('t');
		$query = $qb->select('t.title, t.name, count(c.id) as codes')
						->leftJoin('Govnokod\CodeBundle\Entity\Code', 'c', 'WITH', 't.id = c.category')
						->orderBy('codes', 'DESC')
						->groupBy('t.id')
						->getQuery();

		$categories = $query->getResult();
		return $this->render('GovnokodCodeBundle:Category:list.html.twig', array(
			'categories' => $categories
		));
	}
}