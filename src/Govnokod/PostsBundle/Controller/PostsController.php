<?php
namespace Govnokod\PostsBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Govnokod\PostsBundle\Entity\Post;
use Govnokod\CommentBundle\Entity\Thread;
use Symfony\Component\HttpFoundation\Request;

class PostsController extends Controller
{
    public function listAction($category = null)
    {
        $em = $this->getDoctrine()->getManager();

        $criteria = array();
        if (!is_null($category)) {
            $categoryRepository = $em->getRepository('GovnokodPostsBundle:Category');
            $categoryEntity = $categoryRepository->findByName($category);
            if (!$categoryEntity) {
                throw $this->createNotFoundException("Category «{$category}» doesn't exists");
            }
            $criteria['category'] = $categoryEntity;
        }

        $postsRepository = $em->getRepository('GovnokodPostsBundle:Post');
        $posts = $postsRepository->findBy($criteria, array('created_at' => 'DESC'));

        return $this->render('GovnokodPostsBundle:Posts:list.html.twig', array(
            'posts' => $posts
        ));
    }

    public function viewAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $codeRepository = $em->getRepository('GovnokodPostsBundle:Post');

        $post = $codeRepository->find($id);

        if (!$post) {
            throw $this->createNotFoundException('Code not found');
        }

        if ($request->get('_route') == 'code_view_legacy') {
            return $this->redirect($this->generateUrl('post_view', array('category' => $post->getCategory()->getName(), 'id' => $post->getId())), 301);
        }

        $commentThreadRepository = $em->getRepository('GovnokodCommentBundle:Thread');
        $thread = $commentThreadRepository->findOneBy(array(
            'target_type' => 'post',
            'target_id'   => $post->getId()
        ));

        if (!$thread) {
            $thread = new Thread();
            $thread->setTargetType('code');
            $thread->setTargetId($post->getId());
        }

        return $this->render('GovnokodPostsBundle:Posts:view.html.twig', array(
            'post' => $post
        ));
    }

    public function saveAction(Request $request, $id = null)
    {
        $currentUser = $this->getUser();

        /** @var \Doctrine\ORM\EntityManager $em */
        $em = $this->getDoctrine()->getManager();

        $codeRepository = $em->getRepository('GovnokodPostsBundle:Post');

        if ($id === null) {
            $post = new Post();
            $post->setUser($currentUser);
        } else {
            $post = $codeRepository->find($id);
            if (!$post) {
                throw $this->createNotFoundException('Code not found');
            }
        }

        $categoryRepository = $em->getRepository('GovnokodPostsBundle:Category');

        /* @var $categories \Govnokod\PostsBundle\Entity\Category[] */
        $categories = $categoryRepository->createQueryBuilder('c')
            ->orderBy('c.title', 'ASC')
            ->getQuery()
            ->getResult();

        $form = $this->createFormBuilder($post)
            ->add('category', 'entity', array(
                'class' => 'GovnokodPostsBundle:Category',
                'choices' => $categories,
                'property' => 'title'
            ))
            ->add('body', 'textarea', array(
                'required' => false
            ))
            ->add('description', 'textarea', array(
                'required' => false
            ))
            ->add('tags', 'text', array(
                'property_path' => 'tags_string',
                'required' => false
            ))
            ->getForm()
        ;

        if ($request->isMethod('POST')) {
            $form->submit($request);

            if ($form->isValid()) {
                $em->persist($post);
                $em->flush();

                return $this->redirect($this->generateUrl('post_view', array(
                    'category' => $post->getCategory()->getName(),
                    'id' => $post->getId()
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

        return $this->render('GovnokodPostsBundle:Posts:save.html.twig', array(
            'post' => $post,
            'highlighters' => $highlighters,
            'form' => $form->createView()
        ));
    }
}
