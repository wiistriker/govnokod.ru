<?php

namespace Govnokod\CodeBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Govnokod\CodeBundle\Entity\Code;
use Govnokod\CommentBundle\Entity\Thread;
use Symfony\Component\HttpFoundation\Request;

class CodeController extends Controller
{
    public function listAction(Request $request, $category = null)
    {
        /* var $codeService \Govnokod\CodeBundle\Service\CodeService */
        $codeService = $this->container->get('code_service');
        /* var $categoryService \Govnokod\CodeBundle\Service\CategoryService */
        $categoryService = $this->container->get('category_service');
        $categoryEntity = null;

        if ($category) {
            $categoryEntity = $categoryService->getEntity($category);
            if (!$categoryEntity) {
                throw $this->createNotFoundException("Category «{$category}» doesn't exists");
            }
        }

        $page = $request->query->get('page', 0);
        $pageSize = $this->container->getParameter('code_controller.page_size');

        list($codes, $pageCount, $actualPage) = $codeService->paginate($page, $pageSize, $categoryEntity);

        return $this->render('GovnokodCodeBundle:Code:list.html.twig', [
            'codes' => $codes,
            'currentPage' => $actualPage,
            'pageCount' => $pageCount,
            'current_filters' => ['category' => $category]
        ]);
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
