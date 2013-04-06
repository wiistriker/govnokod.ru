<?php

namespace Govnokod\CodeBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Govnokod\CodeBundle\Entity\Code;

class CodeController extends Controller
{
    public function listAction($category = null)
    {
        $em = $this->getDoctrine()->getManager();

        $criteria = array();
        if (!is_null($category)) {
            $categoryRepository = $em->getRepository("GovnokodCodeBundle:Category");
            $categoryEntity = $categoryRepository->findByName($category);
            if (!$categoryEntity) {
                throw $this->createNotFoundException("Category «{$category}» doesn't exists");
            }
            $criteria['category'] = $categoryEntity;
        }

        $codeRepository = $em->getRepository('GovnokodCodeBundle:Code');
        $codes = $codeRepository->findBy($criteria, array('created_at' => 'DESC'));

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

        if ($request->get('_route') == 'code_view_legacy') {
            return $this->redirect($this->generateUrl('code_view', array('category' => $code->getCategory()->getName(), 'id' => $code->getId())), 301);
        }

        return $this->render('GovnokodCodeBundle:Code:view.html.twig', array(
            'code' => $code
        ));
    }

    public function saveAction($id = null)
    {
        $request = $this->getRequest();
        $em = $this->getDoctrine()->getManager();

        $codeRepository = $em->getRepository('GovnokodCodeBundle:Code');

        if ($id === null) {
            $code = new Code();
        } else {
            $code = $codeRepository->find($id);
            if (!$code) {
                throw $this->createNotFoundException('Code not found');
            }
        }

        $form = $this->createFormBuilder($code)
            ->add('category', 'entity', array(
                'class' => 'GovnokodCodeBundle:Category',
                'property' => 'title'
            ))
            ->add('body', 'textarea', array(
                'required' => false
            ))
            ->add('description', 'textarea', array(
                'required' => false
            ))
            ->getForm()
        ;

        if ($request->isMethod('POST')) {
            $form->bind($request);

            if ($form->isValid()) {
                $em->persist($code);
                $em->flush();

                return $this->redirect($this->generateUrl('code_view', array(
                    'category' => $code->getCategory()->getName(),
                    'id' => $code->getId()
                )));
            }
        }

        return $this->render('GovnokodCodeBundle:Code:save.html.twig', array(
            'code' => $code,
            'form' => $form->createView()
        ));
    }
}
