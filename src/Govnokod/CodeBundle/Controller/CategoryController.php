<?php

namespace Govnokod\CodeBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class CategoryController extends Controller
{
	public function listAction()
	{
		$repository = $this->getDoctrine()->getRepository("GovnokodCodeBundle:Category");
		/* @var $qb \Doctrine\ORM\QueryBuilder */
		$qb = $repository->createQueryBuilder('t');
		$query = $qb->select("t.title, t.name, count(c.id) as codes")
						->leftJoin('Govnokod\CodeBundle\Entity\Code', "c", "WITH", "t.id = c.category")
						->orderBy("codes")
						->groupBy("c.id")
						->getQuery();

		$categories = $query->getResult();
		return $this->render('GovnokodCodeBundle:Category:list.html.twig', array(
			'categories' => $categories
		));
	}

	public function viewAction($category) {
		$categoryRepository = $this->getDoctrine()->getRepository("GovnokodCodeBundle:Category");
		$categoryEntity = $categoryRepository->findBy(array("name" => $category));
		if (!$categoryEntity) {
			throw $this->createNotFoundException("Category «{$category}» doesn't exists");
		}
		$codeRepository = $this->getDoctrine()->getRepository('GovnokodCodeBundle:Code');
		$codes = $codeRepository->findBy(array("category" => $categoryEntity), array("created_at" => "DESC"));
		return $this->render('GovnokodCodeBundle:Code:list.html.twig', array(
			'codes' => $codes
		));
	}
}