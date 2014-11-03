<?php
namespace Govnokod\CodeBundle\DataFixtures\ORM;

use Govnokod\CodeBundle\Entity\Language;
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;

class LoadCodeData extends AbstractFixture implements OrderedFixtureInterface
{
    /* @var $categories \Govnokod\CodeBundle\Entity\Language[] */
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
        foreach ($this->categories as $c) {
            $category = new Language();
            $category->setName($c['name']);
            $category->setTitle($c['title']);
            $category->setCmHighlighter($c['highlighter']);
            $category->setCmMime($c['mime']);

            $manager->persist($category);
        }

        $manager->flush();
    }

    /**
     * {@inheritDoc}
     */
    public function getOrder()
    {
        return 5;
    }
}
