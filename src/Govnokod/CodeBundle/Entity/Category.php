<?php

namespace Govnokod\CodeBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Govnokod\CodeBundle\Entity\CodeCategory
 *
 * @ORM\Table(name="code_category")
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
     * @ORM\OneToMany(targetEntity="Govnokod\CodeBundle\Entity\Code", mappedBy="category")
     */
    protected $codes;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->codes = new \Doctrine\Common\Collections\ArrayCollection();
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
     * Add codes
     *
     * @param  \Govnokod\CodeBundle\Entity\Code $codes
     * @return Category
     */
    public function addCode(\Govnokod\CodeBundle\Entity\Code $codes)
    {
        $this->codes[] = $codes;

        return $this;
    }

    /**
     * Remove codes
     *
     * @param \Govnokod\CodeBundle\Entity\Code $codes
     */
    public function removeCode(\Govnokod\CodeBundle\Entity\Code $codes)
    {
        $this->codes->removeElement($codes);
    }

    /**
     * Get codes
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getCodes()
    {
        return $this->codes;
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
