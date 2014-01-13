<?php
namespace JC\GoodsBundle\DataFixtures\ORM;

use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

class LoadUserData extends AbstractFixture implements OrderedFixtureInterface, ContainerAwareInterface
{
    private $container;

    public function setContainer(ContainerInterface $container = null)
    {
        $this->container = $container;
    }

    /**
     * {@inheritDoc}
     */
    public function load(ObjectManager $manager)
    {
        $userManager = $this->container->get('fos_user.user_manager');

        $userAdmin = $userManager->createUser();
        $userAdmin->setEnabled(true);
        $userAdmin->setUsername('admin');
        $userAdmin->setEmail('admin@govnokod.ru');
        $userAdmin->setPlainPassword('123');
        $userAdmin->addRole('ROLE_SUPER_ADMIN');

        $userManager->updateUser($userAdmin);

        $this->addReference('user-admin', $userAdmin);
    }

    /**
     * {@inheritDoc}
     */
    public function getOrder()
    {
        return 0;
    }
}
