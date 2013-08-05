<?php

namespace Govnokod\UserBundle\Security\Core\User;

use HWI\Bundle\OAuthBundle\Security\Core\Exception\AccountNotConnectedException;
use Symfony\Component\DependencyInjection\ContainerInterface;
use FOS\UserBundle\Model\UserManagerInterface;
use Doctrine\ORM\EntityManager;
use HWI\Bundle\OAuthBundle\Security\Core\User\OAuthAwareUserProviderInterface;
use HWI\Bundle\OAuthBundle\Security\Core\Exception\AccountNotLinkedException;
use HWI\Bundle\OAuthBundle\OAuth\Response\UserResponseInterface;

class OAuthUserProvider implements OAuthAwareUserProviderInterface
{
    /**
     * @var UserManagerInterface
     */
    protected $userManager;

    /**
     * @var EntityManager
     */
    protected $em;

    /**
     * Constructor.
     *
     * @param EntityManager $em Entity manager
     */
    public function __construct(UserManagerInterface $userManager, EntityManager $em)
    {
        $this->userManager = $userManager;
        $this->em = $em;
    }

    public function loadUserByOAuthUserResponse(UserResponseInterface $response)
    {
        $resourceOwner = $response->getResourceOwner();
        $service_user_id = $response->getUsername();

        $user = null;

        $userSignupRepository = $this->em->getRepository('GovnokodUserBundle:UserSignup');
        $userSignup = $userSignupRepository->findOneBy(array(
            'service' => $resourceOwner->getName(),
            'service_user_id' => $service_user_id
        ));

        if ($userSignup) {
            $user = $this->userManager->findUserBy(array('id' => $userSignup->getUserId()));
        }

        if ($user === null) {
            throw new AccountNotLinkedException('User not found');
        }

        return $user;
    }

    public function connect($user, UserResponseInterface $response)
    {
        $resourceOwner = $response->getResourceOwner();
        $service_user_id = $response->getUsername();

        $userSignupRepository = $this->em->getRepository('GovnokodUserBundle:UserSignup');
        $userSignup = $userSignupRepository->findOneBy(array(
            'service' => $resourceOwner->getName(),
            'service_user_id' => $service_user_id
        ));

        if (!$userSignup) {
            $userSignup = new UserSignup();
            $userSignup->setService($resourceOwner->getName());
            $userSignup->setServiceUserId($service_user_id);
            $userSignup->setUserId($user->getId());

            $data = array(
                'access_token' => $response->getAccessToken()
            );

            //$userSignup->setData(json_encode($data));

            $this->em->persist($userSignup);
            $this->em->flush();
        }
    }
}