<?php

namespace Govnokod\RatingsBundle\Controller;

use Govnokod\RatingsBundle\Entity\Rate;
use Govnokod\RatingsBundle\Entity\RatingTarget;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class RateController extends Controller
{
    public function codeVoteAction(Request $request, $code_id, $rate)
    {
        $currentUser = $this->getUser();

        $em = $this->getDoctrine()->getManager();

        $codeRepository = $em->getRepository('GovnokodCodeBundle:Code');

        /** @var \Govnokod\CodeBundle\Entity\Code $code */
        $code = $codeRepository->find($code_id);

        if (!$code) {
            throw $this->createNotFoundException();
        }

        $codeAuthor = $code->getUser();

        $rateObject = null;

        /** @var \Govnokod\RatingsBundle\Entity\RateRepository $rateRepository */
        $rateRepository = $em->getRepository('GovnokodRatingsBundle:Rate');

        $rateObject = $rateRepository->findOneBy(array(
            'targetType' => 'code',
            'targetId' => $code->getId(),
            'user' => $currentUser
        ));

        if (!$rateObject) {
            $rateObject = new Rate();
            $rateObject->setTargetType('code');
            $rateObject->setTargetId($code->getId());
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
                $code->changeRating($rate_value);

                $em->persist($code);

                if ($codeAuthor) {
                    $codeAuthor->changeRating($rate_value);
                    $em->persist($codeAuthor);
                }

                $em->persist($rateObject);
                $em->flush();
            }
        }

        return $this->render('GovnokodRatingsBundle:Rate/Code:rating.html.twig', array(
            'code' => $code,
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
                    $commentAuthor->changeRating($rate_value);
                    $em->persist($commentAuthor);
                }

                $em->persist($rateObject);
                $em->flush();
            }
        }

        return $this->render('GovnokodRatingsBundle:Rate/Comment:rating.html.twig', array(
            'comment' => $comment,
            'rate' => $rate
        ));
    }
}
