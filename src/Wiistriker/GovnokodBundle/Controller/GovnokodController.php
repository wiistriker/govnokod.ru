<?php

namespace Wiistriker\GovnokodBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;


class GovnokodController extends Controller
{
    public function addAction()
    {
        return $this->render('WiistrikerGovnokodBundle:Govnokod:index.html.twig');
    }
}
