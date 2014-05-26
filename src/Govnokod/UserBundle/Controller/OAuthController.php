<?php
namespace Govnokod\UserBundle\Controller;

use HWI\Bundle\OAuthBundle\Security\Core\Exception\AccountNotConnectedException;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Http\Event\InteractiveLoginEvent;
use Symfony\Component\Security\Http\SecurityEvents;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Security\Core\SecurityContextInterface;
use HWI\Bundle\OAuthBundle\OAuth\Response\UserResponseInterface;
use FOS\UserBundle\Model\UserInterface;
use HWI\Bundle\OAuthBundle\OAuth\Response\AdvancedUserResponseInterface;
use HWI\Bundle\OAuthBundle\OAuth\ResourceOwnerInterface;
use Govnokod\UserBundle\Entity\User;
use Govnokod\UserBundle\Entity\UserSignup;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Component\Security\Core\Exception\AccountStatusException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use HWI\Bundle\OAuthBundle\Security\Core\Exception\AccountNotLinkedException;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class OAuthController extends Controller
{
    /**
     * @param string  $service
     *
     * @return RedirectResponse
     */
    public function redirectToServiceAction($service)
    {
        $request = $this->getRequest();

        if ($request->hasSession() && $target_path = $request->get('_back', null, true)) {
            $provider_key = $this->container->getParameter('hwi_oauth.firewall_name');
            $request->getSession()->set('_security.' . $provider_key . '.target_path', $target_path);
        }

        return new RedirectResponse($this->container->get('hwi_oauth.security.oauth_utils')->getAuthorizationUrl($request, $service));
    }

    public function oauthLoginAction(Request $request)
    {
        $connect = $this->container->getParameter('hwi_oauth.connect');
        $hasUser = $this->container->get('security.context')->isGranted('IS_AUTHENTICATED_REMEMBERED');

        $error = $this->getErrorForRequest($request);

        if (true) {
            if (!$hasUser && $error instanceof AccountNotLinkedException) {
                $key = time();
                $session = $request->getSession();
                $session->set('_hwi_oauth.registration_error.'.$key, $error);

                return $this->redirect($this->generateUrl('oauth_registration', array('key' => $key)));
            } else if ($hasUser && $error instanceof AccountNotConnectedException) {
                $key = time();
                //$session = $request->getSession();
                //$session->set('_hwi_oauth.registration_error.'.$key, $error);

                return $this->redirect($this->generateUrl('homepage', array('key' => $key)));
            }
        }

        /*
        // if connecting is enabled and there is no user, redirect to the registration form
        if ($connect && !$hasUser && $error instanceof AccountNotLinkedException) {
            $key = time();
            $session = $request->getSession();
            $session->set('_hwi_oauth.registration_error.'.$key, $error);

            return $this->redirect($this->generateUrl('hwi_oauth_connect_registration', array('key' => $key)));
        }
        */

        $session = $request->getSession();
        $session->set(SecurityContextInterface::AUTHENTICATION_ERROR, $error);

        return $this->redirect($this->generateUrl('fos_user_security_login'));
    }

    /**
     * Connects a user to a given account if the user is logged in and connect is enabled.
     *
     * @param Request $request The active request.
     * @param string  $service Name of the resource owner to connect to.
     *
     * @return Response
     */
    public function connectServiceAction(Request $request, $service)
    {
        $user = $this->getUser();
        $em = $this->getDoctrine()->getManager();

        $hasUser = $this->container->get('security.context')->isGranted('IS_AUTHENTICATED_REMEMBERED');
        $connect = $this->container->getParameter('hwi_oauth.connect');

        if (!$connect || !$hasUser) {
            throw new AccessDeniedException();
            //throw new \Exception('Cannot connect an account.');
        }

        // Get the data from the resource owner
        $resourceOwner = $this->getResourceOwnerByName($service);

        $session = $request->getSession();
        $key = $request->query->get('key', time());

        if ($resourceOwner->handles($request)) {
            $accessToken = $resourceOwner->getAccessToken(
                $request,
                $this->generateUrl('hwi_oauth_connect_service', array('service' => $service), true)
            );

            // save in session
            $session->set('_hwi_oauth.connect_confirmation.'.$key, $accessToken);
        } else {
            $accessToken = $session->get('_hwi_oauth.connect_confirmation.'.$key);
        }

        $userInformation = $resourceOwner->getUserInformation($accessToken);

        // Handle the form
        $form = $this->container->get('form.factory')
            ->createBuilder('form')
            ->getForm();

        if ($request->isMethod('POST')) {
            $form->bind($request);

            if ($form->isValid()) {
                $this->container->get('jc_user.user_provider.oauth')->connect($user, $userInformation);

                return $this->container->get('templating')->renderResponse('JCUserBundle:Profile:Settings/connect_success.html.twig', array(
                    'userInformation' => $userInformation,
                ));
            }
        }

        return $this->container->get('templating')->renderResponse('JCUserBundle:Profile:Settings/connect_confirm.html.twig', array(
            'key' => $key,
            'service' => $service,
            'form' => $form->createView(),
            'userInformation' => $userInformation,
        ));
    }

    public function registrationAction(Request $request, $key)
    {
        $hasUser = $this->container->get('security.context')->isGranted('IS_AUTHENTICATED_REMEMBERED');
        $connect = $this->container->getParameter('hwi_oauth.connect');
        $provider_key = $this->container->getParameter('hwi_oauth.firewall_name');

        $session = $request->getSession();
        $error = $session->get('_hwi_oauth.registration_error.' . $key);
        $session->remove('_hwi_oauth.registration_error.' . $key);

        $connect = true;

        if (!$connect || $hasUser || !($error instanceof AccountNotLinkedException) || (time() - $key > 300)) {
            $error = new AuthenticationException('Ошибка входа через OAuth. Пожалуйста, попробуйте еще раз.');

            $session->set(SecurityContextInterface::AUTHENTICATION_ERROR, $error);
            return $this->redirect($this->generateUrl('fos_user_security_login'));
        }

        $resourceOwner = $this->getResourceOwnerByName($error->getResourceOwnerName());
        $userInformation = $resourceOwner->getUserInformation($error->getRawToken());

        $userManager = $this->container->get('fos_user.user_manager');

        $user = null;
        $user_email = $userInformation->getEmail();

        //если такой email уже есть в базе, то просто свяжем социальный профиль с существующим
        if ($user_email) {
            $user = $userManager->findUserBy(array('email' => $user_email));

            if ($user) {
                $this->authenticateUser($user);
                $this->container->get('jc_user.user_provider.oauth')->connect($user, $userInformation);
                //$this->createNewSignup($user, $resourceOwner, $userInformation);

                if ($session->has('_security.' . $provider_key . '.target_path')) {
                    $target_path = $session->get('_security.' . $provider_key . '.target_path');
                    $session->remove('_security.' . $provider_key . '.target_path');
                } else {
                    $target_path = $this->generateUrl('fos_user_profile_show');
                }

                return $this->redirect($target_path);
            }
        }

        $user = new User();

        $form = $this->createFormBuilder($user, array(
                'validation_groups' => array('RegistrationLite'),
                'csrf_protection' => false
            ))
            ->add('username', null, array('label' => 'form.username', 'translation_domain' => 'FOSUserBundle'))
            ->add('email', 'email', array('label' => 'form.email', 'translation_domain' => 'FOSUserBundle', 'required' => false))
            ->getForm()
        ;

        if ($request->isMethod('POST')) {
            $form->submit($request);

            if ($form->isValid()) {
                $userManager->updateUser($user);
                $this->authenticateUser($user);
                $this->container->get('jc_user.user_provider.oauth')->connect($user, $userInformation);

                if ($session->has('_security.' . $provider_key . '.target_path')) {
                    $target_path = $session->get('_security.' . $provider_key . '.target_path');
                    $session->remove('_security.' . $provider_key . '.target_path');
                } else {
                    $target_path = $this->generateUrl('fos_user_profile_show');
                }

                return $this->redirect($target_path);
            }
        } else {
            //попробуем тут завалидировать форму с теми данными, которые пришли от провайдера.

            //сгенерируем уникальный username на основе данных от провайдера
            $user->setUsername($userInformation->getNickname());

            $unique_username = $user->getUsername();

            for ($i = 1; $i < 4; $i++) {
                $testUser = $userManager->findUserByUsername($unique_username);
                if ($testUser) {
                    $unique_username = $user->getUsername() . $i;
                } else {
                    $testUser = null;
                    break;
                }
            }

            if ($testUser) {
                $unique_username = $user->getUsername();
            }

            $data = array(
                'username' => $unique_username,
                'email' => $user_email
            );

            $form->submit($data);
            if ($form->isValid()) {
                $user->setPassword('ololo');
                $userManager->updateUser($user);
                $this->authenticateUser($user);
                $this->container->get('govnokod.oauth.user_provider')->connect($user, $userInformation);

                if ($session->has('_security.' . $provider_key . '.target_path')) {
                    $target_path = $session->get('_security.' . $provider_key . '.target_path');
                    $session->remove('_security.' . $provider_key . '.target_path');
                } else {
                    $target_path = $this->generateUrl('fos_user_profile_show');
                }

                return $this->redirect($target_path);
            }
        }

        $key = time();
        $session->set('_hwi_oauth.registration_error.' . $key, $error);

        return $this->render('JCUserBundle:OAuth:registration.html.twig', array(
            'key' => $key,
            'form' => $form->createView(),
        ));
    }

    /*
    protected function createNewSignup(User $user, ResourceOwnerInterface $resourceOwner, UserResponseInterface $userInformation)
    {
        $em = $this->getDoctrine()->getManager();

        $userSignup = new UserSignup();
        $userSignup->setService($resourceOwner->getName());
        $userSignup->setServiceUserId($userInformation->getUsername());
        $userSignup->setUserId($user->getId());

        $data = array(
            'access_token' => $userInformation->getAccessToken()
        );

        //$userSignup->setData(json_encode($data));

        $em->persist($userSignup);
        $em->flush();
    }
    */

    /**
     * Authenticate a user with Symfony Security
     *
     * @param UserInterface $user
     */
    protected function authenticateUser(UserInterface $user)
    {
        try {
            $this->container->get('hwi_oauth.user_checker')->checkPostAuth($user);
        } catch (AccountStatusException $e) {
            // Don't authenticate locked, disabled or expired users
            return;
        }

        $providerKey = $this->container->getParameter('hwi_oauth.firewall_name');
        $token = new UsernamePasswordToken($user, null, $providerKey, $user->getRoles());

        $this->container->get('security.context')->setToken($token);

        // Since we're "faking" normal login, we need to throw our INTERACTIVE_LOGIN event manually
        $this->container->get('event_dispatcher')->dispatch(
            SecurityEvents::INTERACTIVE_LOGIN,
            new InteractiveLoginEvent($this->getRequest(), $token)
        );
    }

    /**
     * Get a resource owner by name.
     *
     * @param string $name
     *
     * @return ResourceOwnerInterface
     *
     * @throws \RuntimeException if there is no resource owner with the given name.
     */
    protected function getResourceOwnerByName($name)
    {
        $ownerMap = $this->container->get('hwi_oauth.resource_ownermap.'.$this->container->getParameter('hwi_oauth.firewall_name'));

        if (null === $resourceOwner = $ownerMap->getResourceOwnerByName($name)) {
            throw new \RuntimeException(sprintf("No resource owner with name '%s'.", $name));
        }

        return $resourceOwner;
    }

    /**
     * Get the security error for a given request.
     *
     * @param Request $request
     *
     * @return string|Exception
     */
    protected function getErrorForRequest(Request $request)
    {
        $session = $request->getSession();
        if ($request->attributes->has(SecurityContextInterface::AUTHENTICATION_ERROR)) {
            $error = $request->attributes->get(SecurityContextInterface::AUTHENTICATION_ERROR);
        } elseif (null !== $session && $session->has(SecurityContextInterface::AUTHENTICATION_ERROR)) {
            $error = $session->get(SecurityContextInterface::AUTHENTICATION_ERROR);
            $session->remove(SecurityContextInterface::AUTHENTICATION_ERROR);
        } else {
            $error = '';
        }

        return $error;
    }
}