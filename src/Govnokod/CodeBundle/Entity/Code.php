<?php

namespace Govnokod\CodeBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Wiistriker\GovnokodBundle\Entity\Code
 *
 * @ORM\Table(name="code")
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
     * @var datetime $created_at
     *
     * @ORM\Column(name="created_at", type="datetime")
     */
    protected $created_at;

    /**
     * @var Govnokod\CodeBundle\Entity\Category $category
     *
     * @ORM\ManyToOne(targetEntity="Govnokod\CodeBundle\Entity\Category", inversedBy="codes")
     * @ORM\JoinColumn(name="category_id", referencedColumnName="id")
     */
    protected $category;

    /**
     * @var Symfony\Component\Security\Core\User\UserInterface
     *
     * @ORM\manyToOne(targetEntity="Govnokod\UserBundle\Entity\User")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id")
     */
    protected $user;

    /**
     * @var text $text
     *
     * @ORM\Column(name="body", type="text")
     */
    protected $body = '';

    /**
     * @var text $description
     *
     * @ORM\Column(name="description", type="text")
     */
    protected $description = '';

    /**
     * @ORM\PrePersist
     */
    public function PrePersist()
    {
        if (!$this->created_at) {
            $this->created_at = new \DateTime();
        }
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
     * Set created_at
     *
     * @param \DateTime $createdAt
     * @return Code
     */
    public function setCreatedAt($createdAt)
    {
        $this->created_at = $createdAt;

        return $this;
    }

    /**
     * Get created_at
     *
     * @return \DateTime
     */
    public function getCreatedAt()
    {
        return $this->created_at;
    }

    /**
     * Set body
     *
     * @param string $body
     * @return Code
     */
    public function setBody($body)
    {
        $this->body = (string)$body;

        return $this;
    }

    /**
     * Get body
     *
     * @return string
     */
    public function getBody()
    {
        return $this->body;
    }

    /**
     * Set description
     *
     * @param string $description
     * @return Code
     */
    public function setDescription($description)
    {
        $this->description = (string)$description;

        return $this;
    }

    /**
     * Get description
     *
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Set category
     *
     * @param \Govnokod\CodeBundle\Entity\Category $category
     * @return Code
     */
    public function setCategory(\Govnokod\CodeBundle\Entity\Category $category = null)
    {
        $this->category = $category;

        return $this;
    }

    /**
     * Get category
     *
     * @return \Govnokod\CodeBundle\Entity\Category
     */
    public function getCategory()
    {
        return $this->category;
    }

    /**
     * Set user
     *
     * @param \Wiistriker\UserBundle\Entity\User $user
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
     * @return \Wiistriker\UserBundle\Entity\User
     */
    public function getUser()
    {
        return $this->user;
    }
}