<?php

namespace Wiistriker\GovnokodBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Wiistriker\GovnokodBundle\Entity\Code
 *
 * @ORM\Table(name="govnokod_code")
 * @ORM\Entity
 * @ORM\HasLifecycleCallbacks()
 */
class Code
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
     * @var datetime $created
     *
     * @ORM\Column(name="created", type="datetime")
     */
    protected $created;

    /**
     * @var integer $category_id
     *
     * @ORM\Column(name="category_id", type="integer")
     */
    protected $category_id;

    /**
     * @var Wiistriker\GovnokodBundle\Entity\CodeCategory $category
     *
     * @ORM\manyToOne(targetEntity="Wiistriker\GovnokodBundle\Entity\CodeCategory")
     * @ORM\JoinColumn(name="category_id", referencedColumnName="id")
     */
    protected $category;

    /**
     * @var integer $user_id
     *
     * @ORM\Column(name="user_id", type="integer")
     */
    protected $user_id;

    /**
     * @var Symfony\Component\Security\Core\User\UserInterface
     *
     * @ORM\manyToOne(targetEntity="Wiistriker\UserBundle\Entity\User")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id")
     */
    protected $user;

    /**
     * @var text $text
     *
     * @ORM\Column(name="text", type="text")
     */
    protected $text;

    /**
     * @var text $description
     *
     * @ORM\Column(name="description", type="text")
     */
    protected $description;

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
     * Set created
     *
     * @param datetime $created
     * @return Code
     */
    public function setCreated($created)
    {
        $this->created = $created;
        return $this;
    }

    /**
     * Get created
     *
     * @return datetime
     */
    public function getCreated()
    {
        return $this->created;
    }

    /**
     * Set category_id
     *
     * @param integer $categoryId
     * @return Code
     */
    public function setCategoryId($categoryId)
    {
        $this->category_id = $categoryId;
        return $this;
    }

    /**
     * Get category_id
     *
     * @return integer
     */
    public function getCategoryId()
    {
        return $this->category_id;
    }

    /**
     * Set user_id
     *
     * @param integer $userId
     * @return Code
     */
    public function setUserId($userId)
    {
        $this->user_id = $userId;
        return $this;
    }

    /**
     * Get user_id
     *
     * @return integer
     */
    public function getUserId()
    {
        return $this->user_id;
    }

    /**
     * Set text
     *
     * @param text $text
     * @return Code
     */
    public function setText($text)
    {
        $this->text = $text;
        return $this;
    }

    /**
     * Get text
     *
     * @return text
     */
    public function getText()
    {
        return $this->text;
    }

    /**
     * Set description
     *
     * @param text $description
     * @return Code
     */
    public function setDescription($description)
    {
        $this->description = $description;
        return $this;
    }

    /**
     * Get description
     *
     * @return text
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Set category
     *
     * @param Wiistriker\GovnokodBundle\Entity\CodeCategory $category
     * @return Code
     */
    public function setCategory(\Wiistriker\GovnokodBundle\Entity\CodeCategory $category = null)
    {
        $this->category = $category;
        return $this;
    }

    /**
     * Get category
     *
     * @return Wiistriker\GovnokodBundle\Entity\CodeCategory
     */
    public function getCategory()
    {
        return $this->category;
    }

    /**
     * Set user
     *
     * @param Wiistriker\UserBundle\Entity\User $user
     * @return Code
     */
    public function setUser(\Wiistriker\UserBundle\Entity\User $user = null)
    {
        $this->user = $user;
        return $this;
    }

    /**
     * Get user
     *
     * @return Wiistriker\UserBundle\Entity\User
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * @ORM\PrePersist
     */
    public function setCreatedValue()
    {
        $this->created = new \DateTime();
    }
}