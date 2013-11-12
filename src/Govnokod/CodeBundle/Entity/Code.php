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
     * @var string $updated_at
     *
     * @ORM\Column(name="dateupd", type="datetime", columnDefinition="TIMESTAMP ON UPDATE CURRENT_TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP")
     */
    private $updated_at;

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
     * @var integer $comments_count
     *
     * @ORM\Column(name="comments_count", type="integer", columnDefinition="INT(11) UNSIGNED NOT NULL DEFAULT 0")
     */
    protected $comments_count = 0;

    /**
     * @var float $rating
     *
     * @ORM\Column(name="rating", type="float", columnDefinition="FLOAT NOT NULL DEFAULT 0")
     */
    protected $rating = 0;

    /**
     * @var integer $votes_on
     *
     * @ORM\Column(name="votes_on", type="integer", columnDefinition="INT(11) UNSIGNED NOT NULL DEFAULT 0")
     */
    protected $votes_on = 0;

    /**
     * @var integer $votes_against
     *
     * @ORM\Column(name="votes_against", type="integer", columnDefinition="INT(11) UNSIGNED NOT NULL DEFAULT 0")
     */
    protected $votes_against = 0;

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

    /**
     * Set comments_count
     *
     * @param integer $commentsCount
     * @return Code
     */
    public function setCommentsCount($commentsCount)
    {
        $this->comments_count = (int)$commentsCount;

        return $this;
    }

    /**
     * Get comments_count
     *
     * @return integer
     */
    public function getCommentsCount()
    {
        return $this->comments_count;
    }

    /**
     * Set updated_at
     *
     * @param \DateTime $updatedAt
     * @return Code
     */
    public function setUpdatedAt($updatedAt)
    {
        $this->updated_at = $updatedAt;
    
        return $this;
    }

    /**
     * Get updated_at
     *
     * @return \DateTime 
     */
    public function getUpdatedAt()
    {
        return $this->updated_at;
    }

    /**
     * Set rating
     *
     * @param float $rating
     * @return Code
     */
    public function setRating($rating)
    {
        $this->rating = $rating;
    
        return $this;
    }

    /**
     * Get rating
     *
     * @return float 
     */
    public function getRating()
    {
        return $this->rating;
    }

    /**
     * Set votes_on
     *
     * @param integer $votesOn
     * @return Code
     */
    public function setVotesOn($votesOn)
    {
        $this->votes_on = $votesOn;
    
        return $this;
    }

    /**
     * Get votes_on
     *
     * @return integer 
     */
    public function getVotesOn()
    {
        return $this->votes_on;
    }

    /**
     * Set votes_against
     *
     * @param integer $votesAgainst
     * @return Code
     */
    public function setVotesAgainst($votesAgainst)
    {
        $this->votes_against = $votesAgainst;
    
        return $this;
    }

    /**
     * Get votes_against
     *
     * @return integer 
     */
    public function getVotesAgainst()
    {
        return $this->votes_against;
    }
}