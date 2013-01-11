<?php
namespace JC\GoodsBundle\DataFixtures\ORM;

use Wiistriker\GovnokodBundle\Entity\Code;

use Wiistriker\GovnokodBundle\Entity\CodeCategory;
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;

class LoadGovnoData extends AbstractFixture implements OrderedFixtureInterface
{
    /**
     * {@inheritDoc}
     */
    public function load(ObjectManager $manager)
    {
        $phpCategory = new CodeCategory();
        $phpCategory->setName('php');
        $phpCategory->setTitle('PHP');

        $jsCategory = new CodeCategory();
        $jsCategory->setName('javascript');
        $jsCategory->setTitle('JavaScript');

        $code1 = new Code();
        $code1->setCategory($phpCategory);
        $code1->setDescription('lol');
        $code1->setText('<?php echo date(\'c\'); ?>');

        $manager->persist($phpCategory);
        $manager->persist($jsCategory);
        $manager->persist($code1);

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