<?php
namespace Govnokod\UserBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class UserController extends Controller
{
    public function profileAction()
    {
        $currentUser = $this->getUser();

        return $this->render('GovnokodUserBundle:User:profile.html.twig', array(
            'user' => $currentUser
        ));
    }

    public function viewAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $user = $em->find('GovnokodUserBundle:User', $id);

        if (!$user) {
            $this->createNotFoundException('User not found');
        }

        return $this->render('GovnokodUserBundle:User:view.html.twig', array(
            'user' => $user
        ));
    }
}