<?php

namespace Govnokod\CommentBundle\Controller;

use Symfony\Component\Form\FormError;

use Govnokod\CommentBundle\Entity\Comment;
use Govnokod\CommentBundle\Entity\Thread;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

class ThreadController extends Controller
{
    public function listAction()
    {
        $request = $this->getRequest();
        $currentUser = $this->getUser();

        $em = $this->getDoctrine()->getManager();
        $commentThreadRepository = $em->getRepository('GovnokodCommentBundle:Thread');
        $commentRepository = $em->getRepository('GovnokodCommentBundle:Comment');

        $comment = new Comment();
        $comment->setSender($currentUser);
        $comment->setIp($request->getClientIp());

        $route_name = $request->get('_route');
        $route_params = $request->get('_route_params');

        $template = null;
        $template_params = array();

        $comments = array();

        switch ($route_name) {
            case 'code_comments_list':
                /**
                 * @var \Govnokod\CodeBundle\Entity\Code $code
                 */
                $code = null;
                $code_id = $request->attributes->getInt('code_id');
                if ($code_id > 0) {
                    $codeRepository = $em->getRepository('GovnokodCodeBundle:Code');
                    $code = $codeRepository->find($code_id);
                }

                if (!$code) {
                    throw $this->createNotFoundException('Code not found for id ' . $code_id);
                }

                $template_params['code_id'] = $code_id;
                $template_params['code'] = $code;

                $thread = $commentThreadRepository->findOneBy(array(
                    'target_type' => 'code',
                    'target_id' => $code->getId()
                ));

                if ($thread) {
                    $comments = $commentRepository->findBy(array(
                        'thread' => $thread->getId()
                    ), array(
                        'path' => 'ASC',
                        'created_at' => 'ASC'
                    ));

                    /*
                    $commentsQB = $commentRepository->getChildrenQueryBuilder(null, false, 'created_at');
                    $commentsQB->andWhere('materialized_path_entity.thread = :thread')->setParameter('thread', $thread->getId());

                    $comments = $commentsQB->getQuery()->getResult();
                    */

                    /*
                    $commentsQuery = $commentRepository->createQueryBuilder('c')
                        ->where('c.thread = :thread')
                        ->setParameter('thread', $thread->getId())
                        ->getQuery()
                    ;
                    */

                    /*
                    $templating = $this->container->get('templating');

                    $html_tree = $commentRepository->buildTree($commentsQuery->getArrayResult(), array(
                        'decorate' => true,
                        'childOpen' => '<li class="hcomment">',
                        'nodeDecorator' => function($node) use ($templating) {
                            return $templating->render('GovnokodCommentBundle:Thread:Code/comment.html.twig', array(
                                'node' => $node
                            ));
                        }
                    ));

                    //$template_params['html_tree'] = $html_tree;

                    $html_tree = $commentRepository->childrenHierarchy(
                        null,
                        false,
                        array(
                            'decorate' => false,
                            //'representationField' => 'path',
                            'html' => true,
                            'nodeDecorator' => function($node) {
                                return '<a href="/page/'.$node['id'].'">'.$node['body'].'</a>';
                            }
                        )
                    );

                    print_r($html_tree);
                    //exit;
                   */

                    $comments_count = sizeof($comments);

                    $need_flush = false;
                    if ($thread->getCommentsCount() != $comments_count) {
                        $thread->setCommentsCount($comments_count);
                        $em->persist($thread);
                        $need_flush = true;
                    }

                    if ($code->getCommentsCount() != $comments_count) {
                        $code->setCommentsCount($comments_count);
                        $em->persist($code);
                        $need_flush = true;
                    }

                    if ($need_flush) {
                        $em->flush();
                    }
                } else {
                    $thread = new Thread();
                    $thread->setTargetType('code');
                    $thread->setTargetId($code->getId());
                }

                if ($request->isXmlHttpRequest()) {
                    $template = 'GovnokodCommentBundle:Thread:Code/list.ajax.html.twig';
                } else {
                    $template = 'GovnokodCommentBundle:Thread:Code/list.html.twig';
                }
                break;
        }

        $form = $this->createFormBuilder($comment, array(
                'cascade_validation' => true
            ))
            ->add('body', 'textarea', array(
                'label' => 'Текст сообщения:'
            ))
            /*
            ->add('sender', 'user', array(
                //'property_path' => 'sender'
            ))
            */
            ->getForm()
        ;

        $template_params['route_name'] = $route_name;
        $template_params['route_params'] = $route_params;
        $template_params['sender'] = $comment->getSender();

        if ($request->isMethod('POST')) {
            $form->bind($request);

            if ($thread && $request->query->has('reply')) {
                $parentComent = null;

                $reply_id = $request->query->get('reply');
                if ($reply_id > 0) {
                    $parentComent = $commentRepository->find($reply_id);
                }

                //@todo: move to model validation?
                if ($parentComent) {
                    if ($parentComent->getThread() == $thread) {
                        $comment->setParent($parentComent);
                    } else {
                        $form->addError(new FormError('Wrong thread for target comment'));
                    }
                } else {
                    $form->addError(new FormError('No such parent'));
                }
            }

            if ($form->isValid()) {
                /*
                $is_spam = (bool)$form->get('subject')->getData();
                if ($is_spam) {
                    if ($request->isXmlHttpRequest()) {
                        $template_params['form'] = $form->createView();
                        $template_params['comment'] = $comment;
                        $successResponse = $this->render('GovnokodCommentBundle:Thread:new_success.ajax.html.twig', array(
                            'comment' => $comment
                        ));
                    } else {
                        $successResponse = $this->redirect($this->generateUrl($route_name, $route_params));
                    }

                    return $successResponse;
                }
                */

                /*
                $sender = $comment->getSender();
                $new_user_password = null;
                if (!$sender->getId()) {
                    $userUtils = $this->container->get('jc_user.utils');
                    $userUtils->prepareUserForQuickRegister($sender);

                    $new_user_password = $sender->getPlainPassword();
                }

                $userManager = $this->container->get('fos_user.user_manager');
                $userManager->updateUser($sender);
                */

                $thread->addComment($comment);

                switch ($route_name) {
                    case 'code_comments_list':
                        //$product->setLastCommentId($comment->getId());
                        $code->setCommentsCount($thread->getCommentsCount());
                        $em->persist($code);
                        break;
                }

                $em->persist($thread);
                $em->flush();

                if ($request->isXmlHttpRequest()) {
                    $template_params['form'] = $form->createView();
                    $template_params['comment'] = $comment;

                    $successResponse = $this->render('GovnokodCommentBundle:Thread:Code/new_success.ajax.html.twig', $template_params);
                } else {
                    $successResponse = $this->redirect($this->generateUrl($route_name, $route_params));
                }

                /*
                if ($new_user_password) {
                    $this->get('jc_message.mailer')->sendQuickRegisterNotification($sender, $new_user_password);

                    $loginManager = $this->get('fos_user.security.login_manager');
                    $loginManager->loginUser($this->container->getParameter('fos_user.firewall_name'), $sender, $successResponse);
                }
                */

                return $successResponse;
            }

            if ($request->isXmlHttpRequest()) {
                switch ($route_name) {
                    case 'code_comments_list':
                        $template_params['form'] = $form->createView();
                        return $this->render('GovnokodCommentBundle:Thread:Code/form.html.twig', $template_params);
                        break;

                    default:
                        $template_params['form'] = $form->createView();
                        return $this->render('GovnokodCommentBundle:Thread:form.html.twig', $template_params);
                        break;
                }
            }
        }

        if (!$template) {
            $template = 'GovnokodCommentBundle:Thread:list.html.twig';
        }

        $template_params['form'] = $form->createView();
        $template_params['thread'] = $thread;
        $template_params['comments'] = $comments;

        return $this->render($template, $template_params);
    }

    public function answerAction($id)
    {
        $request = $this->getRequest();
        $user = $this->getUser();

        $em = $this->getDoctrine()->getManager();
        $commentRepository = $em->getRepository('GovnokodCommentBundle:Comment');
        $commentThreadRepository = $em->getRepository('GovnokodCommentBundle:Thread');

        $comment = $commentRepository->find($id);

        if (!$comment) {
            throw $this->createNotFoundException('Comment not found');
        }

        $route_name = $request->get('_route');
        $route_params = $request->get('_route_params');

        $template = null;
        $template_params = array();

        switch ($route_name) {
            case 'product_comment_answer':
                /**
                 * @var \JC\GoodsBundle\Entity\Product
                 */
                $product = null;
                $product_id = $request->attributes->getInt('product_id');
                if ($product_id > 0) {
                    $productRepository = $em->getRepository('JCGoodsBundle:Product');
                    $product = $productRepository->find($product_id);
                }

                if (!$product) {
                    throw $this->createNotFoundException('Product not found for id ' . $product_id);
                }

                if (!$this->get('jc_service.authorizer')->canAnswerToProductComment($product)) {
                    throw new AccessDeniedException();
                }

                $template_params['product_id'] = $product_id;
                $template_params['product'] = $product;

                $thread = $commentThreadRepository->findOneBy(array(
                    'target_type' => 'product',
                    'target_id' => $product->getId()
                ));

                if ($request->isXmlHttpRequest()) {
                    $template = 'GovnokodCommentBundle:Thread:Product/answer.ajax.html.twig';
                } else {
                    $template = 'GovnokodCommentBundle:Thread:Product/answer.html.twig';
                }
                break;
        }

        $form = $this->createFormBuilder($comment)
            ->add('answer', 'textarea', array(
                'required' => false
            ))
            ->getForm()
        ;

        if ($request->isMethod('POST')) {
            $form->bind($request);

            if ($form->isValid()) {
                $em->persist($comment);
                $em->flush();

                switch ($route_name) {
                    case 'product_comment_answer':
                        $this->get('jc_message.mailer')->sendProductCommentAnswerNotification($comment, $product);

                        if ($request->isXmlHttpRequest()) {
                            $successResponse = $this->render('GovnokodCommentBundle:Thread:answer_success.ajax.html.twig', array(
                                'comment' => $comment
                            ));
                        } else {
                            $successResponse = $this->redirect($this->generateUrl('product_comments_list', array('product_id' => $product_id)) . '#comment-' . $id);
                        }

                        return $successResponse;
                        break;
                }
            }
        }

        $template_params['form'] = $form->createView();
        $template_params['thread'] = $thread;
        $template_params['comment'] = $comment;
        $template_params['route_name'] = $route_name;
        $template_params['route_params'] = $route_params;

        return $this->render($template, $template_params);
    }
}