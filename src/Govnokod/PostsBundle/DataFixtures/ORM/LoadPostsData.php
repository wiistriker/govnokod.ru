<?php
namespace JC\GoodsBundle\DataFixtures\ORM;

use Govnokod\PostsBundle\Entity\Post;
use Govnokod\PostsBundle\Entity\Category;
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;

class LoadPostsData extends AbstractFixture implements OrderedFixtureInterface
{
    /* @var $categories \Govnokod\PostsBundle\Entity\Category[] */
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

        $post1 = new Post();
        $post1->setCategory($categories[0]);
        $post1->setDescription('lol');
        $post1->setBody('<?php echo date(\'c\'); ?>');

        $post2 = new Post();
        $post2->setCategory($categories[1]);
        $post2->setDescription('lol');
        $post2->setBody('alert(\'lol\');');

        $manager->persist($post1);
        $manager->persist($post2);

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
