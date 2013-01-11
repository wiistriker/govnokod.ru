<?php

namespace Govnokod\CodeBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class CodeController extends Controller
{
    public function listAction()
    {
        $repository = $this->getDoctrine()->getRepository('GovnokodCodeBundle:Code');
        $codes = $repository->findBy(array(), array('created_at' => 'DESC'));

        return $this->render('GovnokodCodeBundle:Code:list.html.twig', array(
            'codes' => $codes
        ));
    }
}
