<?php

namespace Wiistriker\GovnokodBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Wiistriker\GovnokodBundle\Entity\Code;

class GovnokodController extends Controller
{
    public function listAction()
    {
        $repository = $this->getDoctrine()->getRepository('WiistrikerGovnokodBundle:Code');
        $codes = $repository->findBy(array(), array('created' => 'DESC'));

        return $this->render('WiistrikerGovnokodBundle:Code:list.html.twig', array(
            'codes' => $codes
        ));
    }

    public function viewAction($id)
    {
        $repository = $this->getDoctrine()->getRepository('WiistrikerGovnokodBundle:Code');
        $code = $repository->find($id);

        if (!$code) {
            throw $this->createNotFoundException('No code found for id '. $id);
        }

        return $this->render('WiistrikerGovnokodBundle:Code:view.html.twig', array(
            'code' => $code
        ));
    }

    public function addAction(Request $request)
    {
        $code = new Code();

        $form = $this->createFormBuilder($code)
                ->add('text')
                ->add('description')
                ->getForm();

        if ($request->getMethod() == 'POST') {
            $form->bindRequest($request);

            if ($form->isValid()) {
                $categoryRepository = $this->getDoctrine()->getRepository('WiistrikerGovnokodBundle:CodeCategory');
                $category = $categoryRepository->find(1);
                $code->setCategory($category);

                $userRepository = $this->getDoctrine()->getRepository('WiistrikerUserBundle:User');
                $user = $userRepository->find(1);
                $code->setUser($user);

                $em = $this->getDoctrine()->getEntityManager();
                $em->persist($code);
                $em->flush();

                return new Response('ok');
            }
        }

        return $this->render('WiistrikerGovnokodBundle:Code:add.html.twig', array(
            'form' => $form->createView()
        ));
    }
}