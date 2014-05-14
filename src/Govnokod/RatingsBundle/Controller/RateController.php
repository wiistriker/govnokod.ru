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

        /** @var \Govnokod\CodeBundle\Entity\CodeRepository $codeRepository */
        $codeRepository = $em->getRepository('GovnokodCodeBundle:Code');

        /** @var \Govnokod\CodeBundle\Entity\Code $code */
        $code = $codeRepository->find($code_id);

        if (!$code) {
            throw $this->createNotFoundException();
        }

        $codeAuthor = $code->getUser();

        /** @var \Govnokod\RatingsBundle\Entity\RatingTargetRepository $targetRepository */
        $targetRepository = $em->getRepository('GovnokodRatingsBundle:RatingTarget');

        $ratingTarget = $targetRepository->findOneBy(array(
            'targetType' => 'code',
            'targetId' => $code->getId()
        ));

        $rateObject = null;

        if (!$ratingTarget) {
            $ratingTarget = new RatingTarget();
            $ratingTarget->setTargetType('code');
            $ratingTarget->setTargetId($code->getId());

            $em->persist($ratingTarget);
        } else {
            /** @var \Govnokod\RatingsBundle\Entity\RateRepository $rateRepository */
            $rateRepository = $em->getRepository('GovnokodRatingsBundle:Rate');

            $rateObject = $rateRepository->findOneBy(array(
                'target' => $ratingTarget->getId(),
                'user' => $currentUser
            ));
        }

        if (!$rateObject) {
            $rateObject = new Rate();
            $rateObject->setTarget($ratingTarget);
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
                $ratingTarget->changeRating($rate_value);
                $code->changeRating($rate_value);

                $em->persist($ratingTarget);
                $em->persist($code);

                if ($codeAuthor) {
                    $codeAuthor->changeRating($rate_value);
                    $em->persist($codeAuthor);
                }

                $em->persist($rateObject);
                $em->flush();
            }
        }

        return new Response('test');
    }
}
