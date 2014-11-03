<?php

namespace Govnokod\RatingsBundle\Controller;

use Govnokod\RatingsBundle\Entity\Rate;
use Govnokod\RatingsBundle\Entity\RatingTarget;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class RateController extends Controller
{
    public function postVoteAction(Request $request, $post_id, $rate)
    {
        $currentUser = $this->getUser();

        $em = $this->getDoctrine()->getManager();

        /** @var \Govnokod\PostsBundle\Entity\Post $post */
        $post = $em->find('GovnokodPostsBundle:Post', $post_id);

        if (!$post) {
            throw $this->createNotFoundException();
        }

        if ($currentUser) {
            $codeAuthor = $post->getUser();

            $rateObject = null;

            /** @var \Govnokod\RatingsBundle\Entity\RateRepository $rateRepository */
            $rateRepository = $em->getRepository('GovnokodRatingsBundle:Rate');

            $rateObject = $rateRepository->findOneBy(array(
                'targetType' => 'post',
                'targetId' => $post->getId(),
                'user' => $currentUser
            ));

            if (!$rateObject) {
                $rateObject = new Rate();
                $rateObject->setTargetType('post');
                $rateObject->setTargetId($post->getId());
                $rateObject->setUser($currentUser);
                $rateObject->setIpAddress(implode(';', $request->getClientIps()));

                $rate_value = null;

                switch ($rate) {
                    case 'on':
                        $rate_value = 1;
                        break;

                    case 'against':
                        $rate_value = -1;
                        break;
                }

                if ($rate_value) {
                    $rateObject->setValue($rate_value);
                    $post->changeRating($rate_value);

                    $em->persist($post);

                    if ($codeAuthor) {
                        $author_rating = $rate_value;

                        $codeAuthor->changeRating($author_rating);
                        $em->persist($codeAuthor);
                    }

                    $em->persist($rateObject);
                    $em->flush();
                }
            }
        }

        return $this->render('GovnokodRatingsBundle:Rate/Post:rating.html.twig', array(
            'post' => $post,
            'rate' => $rate
        ));
    }

    public function commentVoteAction(Request $request, $comment_id, $rate)
    {
        $currentUser = $this->getUser();

        $em = $this->getDoctrine()->getManager();

        /** @var \Govnokod\CommentBundle\Entity\Comment $comment */
        $comment = $em->find('GovnokodCommentBundle:Comment', $comment_id);

        if (!$comment) {
            throw $this->createNotFoundException();
        }

        if ($currentUser) {
            $commentAuthor = $comment->getSender();

            /** @var \Govnokod\RatingsBundle\Entity\RateRepository $rateRepository */
            $rateRepository = $em->getRepository('GovnokodRatingsBundle:Rate');

            $rateObject = $rateRepository->findOneBy(array(
                'targetType' => 'comment',
                'targetId' => $comment->getId(),
                'user' => $currentUser
            ));

            if (!$rateObject) {
                $rateObject = new Rate();
                $rateObject->setTargetType('comment');
                $rateObject->setTargetId($comment->getId());
                $rateObject->setUser($currentUser);
                $rateObject->setIpAddress(implode(';', $request->getClientIps()));

                $rate_value = null;

                switch ($rate) {
                    case 'on':
                        $rate_value = 1;
                        break;

                    case 'against':
                        $rate_value = -1;
                        break;
                }

                if ($rate_value) {
                    $rateObject->setValue($rate_value);
                    $comment->changeRating($rate_value);

                    $em->persist($comment);

                    if ($commentAuthor) {
                        $author_rating = $rate_value > 0 ? 0.75 : -0.75;

                        $commentAuthor->changeRating($author_rating);
                        $em->persist($commentAuthor);
                    }

                    $em->persist($rateObject);
                    $em->flush();
                }
            }
        } else {
            $rate = null;
        }

        return $this->render('GovnokodRatingsBundle:Rate/Comment:rating.html.twig', array(
            'comment' => $comment,
            'rate' => $rate
        ));
    }
}
