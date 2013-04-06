<?php

namespace Govnokod\CodeBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class CodeController extends Controller
{
    public function listAction()
    {
        $codeRepository = $this->getDoctrine()->getRepository('GovnokodCodeBundle:Code');
        $codes = $codeRepository->findBy(array(), array('created_at' => 'DESC'));

        return $this->render('GovnokodCodeBundle:Code:list.html.twig', array(
            'codes' => $codes
        ));
    }

    public function viewAction($id)
    {
        $request = $this->getRequest();
        $codeRepository = $this->getDoctrine()->getRepository('GovnokodCodeBundle:Code');

        $code = $codeRepository->find($id);

        if (!$code) {
            throw $this->createNotFoundException('Code not found');
        }

        if ($request->get('_route') == 'view_code_legacy') {
            return $this->redirect($this->generateUrl('view_code', array('category' => $code->getCategory()->getName(), 'id' => $code->getId())));
        }

        return $this->render('GovnokodCodeBundle:Code:view.html.twig', array(
            'code' => $code
        ));
    }
}
