<?php
namespace JC\GoodsBundle\DataFixtures\ORM;

use Govnokod\CodeBundle\Entity\Code;
use Govnokod\CodeBundle\Entity\Category;
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;

class LoadCodeData extends AbstractFixture implements OrderedFixtureInterface
{
    /**
     * {@inheritDoc}
     */
    public function load(ObjectManager $manager)
    {
        $phpCategory = new Category();
        $phpCategory->setName('php');
        $phpCategory->setTitle('PHP');

        $jsCategory = new Category();
        $jsCategory->setName('javascript');
        $jsCategory->setTitle('JavaScript');

        $code1 = new Code();
        $code1->setCategory($phpCategory);
        $code1->setDescription('lol');
        $code1->setBody('<?php echo date(\'c\'); ?>');

        $code2 = new Code();
        $code2->setCategory($jsCategory);
        $code2->setDescription('lol');
        $code2->setBody('alert(\'lol\');');

        $manager->persist($phpCategory);
        $manager->persist($jsCategory);
        $manager->persist($code1);
        $manager->persist($code2);

        $manager->flush();
    }

    /**
     * {@inheritDoc}
     */
    public function getOrder()
    {
        return 0;
    }
}