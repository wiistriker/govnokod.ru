<?php
namespace JC\GoodsBundle\DataFixtures\ORM;

use Govnokod\CodeBundle\Entity\Code;
use Govnokod\CodeBundle\Entity\Category;
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;

class LoadCodeData extends AbstractFixture implements OrderedFixtureInterface
{
    /* @var $categories \Govnokod\CodeBundle\Entity\Category[] */
    private $categories = array(
        array(
            'name' => 'php',
            'title' => 'PHP',
            'highlighter' => 'php',
            'mime' => 'application/x-httpd-php-open',
        ),
        array(
            'name' => 'javascript',
            'title' => 'Javascript',
            'highlighter' => 'javascript',
            'mime' => 'text/javascript',
        ),
        array(
            'name' => 'cpp',
            'title' => 'C++',
            'highlighter' => 'clike',
            'mime' => 'text/x-c++src',
        ),
        array(
            'name' => 'unknown',
            'title' => 'Unknown',
            'highlighter' => 'null',
            'mime' => 'text/plain',
        ),
    );

    /**
     * {@inheritDoc}
     */
    public function load(ObjectManager $manager)
    {
        $categories = array();
        foreach ($this->categories as $c) {
            $category = new Category();
            $category->setName($c['name']);
            $category->setTitle($c['title']);
            $category->setCmHighlighter($c['highlighter']);
            $category->setCmMime($c['mime']);

            $categories[] = $category;

            $manager->persist($category);
        }

        $code1 = new Code();
        $code1->setCategory($categories[0]);
        $code1->setDescription('lol');
        $code1->setBody('<?php echo date(\'c\'); ?>');

        $code2 = new Code();
        $code2->setCategory($categories[1]);
        $code2->setDescription('lol');
        $code2->setBody('alert(\'lol\');');

        $manager->persist($code1);
        $manager->persist($code2);

        $manager->flush();
    }

    /**
     * {@inheritDoc}
     */
    public function getOrder()
    {
        return 10;
    }
}
