<?php
namespace Govnokod\UserBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\SecurityContextInterface;

class UserController extends Controller
{
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