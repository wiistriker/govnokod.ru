<?php
namespace Govnokod\UserBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class RegistrationController extends Controller
{
    const CONFIRMATION_EMAIL_SESSION_KEY = 'confirmation_email';

    public function registerAction()
    {
        $form = $this->get('fos_user.registration.form');
        $formHandler = $this->get('fos_user.registration.form.handler');

        $session = $this->get('session');
        $session->set(self::CONFIRMATION_EMAIL_SESSION_KEY, 'wiistriker@gmail.com');
        return $this->redirect($this->generateUrl('user_register_check_email'));

        $process = $formHandler->process(true);
        if ($process) {
            $user = $form->getData();

            $session = $this->get('session');
            $session->set(self::CONFIRMATION_EMAIL_SESSION_KEY, $user->getEmail());

            return $this->redirect($this->generateUrl('user_register_check_email'));
        }

        return $this->render('GovnokodUserBundle:Register:form.html.twig', array(
            'form' => $form->createView(),
        ));
    }

    public function checkEmailAction()
    {
        $session = $this->get('session');

        if (!$session->has(self::CONFIRMATION_EMAIL_SESSION_KEY)) {
            return $this->redirect($this->generateUrl('_index'));
        }

        $email = $session->get(self::CONFIRMATION_EMAIL_SESSION_KEY);
        $session->remove(self::CONFIRMATION_EMAIL_SESSION_KEY);

        $userManager = $this->get('fos_user.user_manager');

        $user = $userManager->findUserByEmail($email);

        if (!$user) {
            return $this->redirect($this->generateUrl('_index'));
        }

        return $this->render('GovnokodUserBundle:Register:check_email.html.twig', array(
            'user' => $user
        ));
    }
} 