<?php
namespace Govnokod\SoftwareBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class SoftwareController extends Controller
{
    public function listAction()
    {
        return $this->render('GovnokodSoftwareBundle:Software:list.html.twig');
    }
} 