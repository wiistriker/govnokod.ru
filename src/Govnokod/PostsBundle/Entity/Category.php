<?php

namespace Govnokod\PostsBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Govnokod\PostsBundle\Entity\CodeCategory
 *
 * @ORM\Table(name="posts_category")
 * @ORM\Entity
 */
class Category
{
    /**
     * @var integer $id
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @var string $name
     *
     * @ORM\Column(name="name", type="string", length=255)
     */
    protected $name;

    /**
     * @var string $title
     *
     * @ORM\Column(name="title", type="string", length=255)
     */
    protected $title;

    /**
     * @var string $cmHighlighter
     *
     * @ORM\Column(name="cm_highlighter", type="string", length=255)
     */

    protected $cmHighlighter;
    /**
     * @var string $cmMime
     *
     * @ORM\Column(name="cm_mime", type="string", length=255)
     */
    protected $cmMime;

    /**
     * @var \Doctrine\Common\Collections\Collection
     *
     * @ORM\OneToMany(targetEntity="Govnokod\PostsBundle\Entity\Post", mappedBy="category")
     */
    protected $posts;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->posts = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set name
     *
     * @param  string   $name
     * @return Category
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set title
     *
     * @param  string   $title
     * @return Category
     */
    public function setTitle($title)
    {
        $this->title = $title;

        return $this;
    }

    /**
     * Get title
     *
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * Add post
     *
     * @param  \Govnokod\PostsBundle\Entity\Post $post
     * @return Category
     */
    public function addPost(\Govnokod\PostsBundle\Entity\Post $post)
    {
        $this->posts[] = $post;

        return $this;
    }

    /**
     * Remove post
     *
     * @param \Govnokod\PostsBundle\Entity\Post $post
     */
    public function removeCode(\Govnokod\PostsBundle\Entity\Post $post)
    {
        $this->posts->removeElement($post);
    }

    /**
     * Get codes
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getPosts()
    {
        return $this->posts;
    }

    /**
     * @param string $cmHighlighter
     */
    public function setCmHighlighter($cmHighlighter)
    {
        $this->cmHighlighter = $cmHighlighter;
    }

    /**
     * @return string
     */
    public function getCmHighlighter()
    {
        return $this->cmHighlighter;
    }

    /**
     * @param string $cmMime
     */
    public function setCmMime($cmMime)
    {
        $this->cmMime = $cmMime;
    }

    /**
     * @return string
     */
    public function getCmMime()
    {
        return $this->cmMime;
    }
}
