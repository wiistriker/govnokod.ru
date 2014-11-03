<?php
namespace Govnokod\CodeBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Language
 *
 * @ORM\Table(name="code_language")
 * @ORM\Entity
 */
class Language
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
     * @return Language
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
     * @return Language
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

    public function getTags()
    {
        switch ($this->getName()) {
            case 'php':
                return array('php');
                break;
        }

        return array();
    }
}
