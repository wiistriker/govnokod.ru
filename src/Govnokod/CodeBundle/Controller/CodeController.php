<?php

namespace Govnokod\CodeBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Govnokod\CodeBundle\Entity\Code;
use Govnokod\CommentBundle\Entity\Thread;
use Symfony\Component\HttpFoundation\Request;

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

    public function viewAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $codeRepository = $em->getRepository('GovnokodCodeBundle:Code');

        $code = $codeRepository->find($id);

        if (!$code) {
            throw $this->createNotFoundException('Code not found');
        }

        if ($request->get('_route') == 'code_view_legacy') {
            return $this->redirect($this->generateUrl('code_view', array('category' => $code->getCategory()->getName(), 'id' => $code->getId())), 301);
        }

        $commentThreadRepository = $em->getRepository('GovnokodCommentBundle:Thread');
        $thread = $commentThreadRepository->findOneBy(array(
            'target_type' => 'code',
            'target_id'   => $code->getId()
        ));

        if (!$thread) {
            $thread = new Thread();
            $thread->setTargetType('code');
            $thread->setTargetId($code->getId());
        }

        return $this->render('GovnokodCodeBundle:Code:view.html.twig', array(
            'code' => $code
        ));
    }

    public function saveAction(Request $request, $id = null)
    {
        $currentUser = $this->getUser();

        /** @var \Doctrine\ORM\EntityManager $em */
        $em = $this->getDoctrine()->getManager();

        $codeRepository = $em->getRepository('GovnokodCodeBundle:Code');

        if ($id === null) {
            $code = new Code();
            $code->setUser($currentUser);
        } else {
            $code = $codeRepository->find($id);
            if (!$code) {
                throw $this->createNotFoundException('Code not found');
            }
        }

        $categoryRepository = $em->getRepository('GovnokodCodeBundle:Category');

        /* @var $categories \Govnokod\CodeBundle\Entity\Category[] */
        $categories = $categoryRepository->createQueryBuilder('c')
            ->orderBy('c.title', 'ASC')
            ->getQuery()
            ->getResult();

        $form = $this->createFormBuilder($code)
            ->add('category', 'entity', array(
                'class' => 'GovnokodCodeBundle:Category',
                'choices' => $categories,
                'property' => 'title'
            ))
            ->add('body', 'textarea', array(
                'required' => false
            ))
            ->add('description', 'textarea', array(
                'required' => false
            ))
            ->add('tags', 'collection', array(
                'type' => 'text',
                'allow_add' => true,
                'allow_delete' => true
            ))
            ->getForm()
        ;

        if ($request->isMethod('POST')) {
            $form->submit($request);

            if ($form->isValid()) {
                $em->persist($code);
                $em->flush();

                return $this->redirect($this->generateUrl('code_view', array(
                    'category' => $code->getCategory()->getName(),
                    'id' => $code->getId()
                )));
            }
        }

        $highlighters = array();

        foreach ($categories as $category) {
            $highlighters[$category->getId()] = array(
                'mode' => $category->getCmHighlighter(),
                'mime' => $category->getCmMime()
            );
        }

        return $this->render('GovnokodCodeBundle:Code:save.html.twig', array(
            'code' => $code,
            'highlighters' => $highlighters,
            'form' => $form->createView()
        ));
    }
}
