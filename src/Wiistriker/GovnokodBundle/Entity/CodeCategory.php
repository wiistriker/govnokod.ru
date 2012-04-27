<?php

namespace Wiistriker\GovnokodBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Wiistriker\GovnokodBundle\Entity\CodeCategory
 *
 * @ORM\Table(name="govnokod_category")
 * @ORM\Entity
 */
class CodeCategory
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
     * @var Doctrine\Common\Collections\Collection
     *
     * @ORM\OneToMany(targetEntity="Wiistriker\GovnokodBundle\Entity\Code", mappedBy="category")
     */
    protected $codes;

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
     * @param string $name
     * @return CodeCategory
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
     * @param string $title
     * @return CodeCategory
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
     * @param Wiistriker\GovnokodBundle\Entity\Code $codes
     * @return CodeCategory
     */
    public function addCode(\Wiistriker\GovnokodBundle\Entity\Code $codes)
    {
        $this->codes[] = $codes;
        return $this;
    }

    /**
     * Get codes
     *
     * @return Doctrine\Common\Collections\Collection
     */
    public function getCodes()
    {
        return $this->codes;
    }
}