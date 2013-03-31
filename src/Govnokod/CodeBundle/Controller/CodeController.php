<?php

namespace Govnokod\CodeBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class CodeController extends Controller
{
    public function listAction($category = null)
    {
        $criteria = array();
        if (!is_null($category)) {
            $categoryRepository = $this->getDoctrine()->getRepository("GovnokodCodeBundle:Category");
            $categoryEntity = $categoryRepository->findBy(array("name" => $category));
            if (!$categoryEntity) {
                throw $this->createNotFoundException("Category «{$category}» doesn't exists");
            }
            $criteria = array('category' => $categoryEntity);
        }

        $codeRepository = $this->getDoctrine()->getRepository('GovnokodCodeBundle:Code');
        $codes = $codeRepository->findBy($criteria, array('created_at' => 'DESC'));

        return $this->render('GovnokodCodeBundle:Code:list.html.twig', array(
            'codes' => $codes
        ));
    }

    public function viewAction($id)
    {
        $codeRepository = $this->getDoctrine()->getRepository('GovnokodCodeBundle:Code');

        $code = $codeRepository->find($id);

        if (!$code) {
            throw $this->createNotFoundException('Code not found');
        }

        return $this->render('GovnokodCodeBundle:Code:view.html.twig', array(
            'code' => $code
        ));
    }
}
