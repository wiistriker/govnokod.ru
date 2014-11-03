<?php

namespace Govnokod\PostsBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * \Govnokod\PostsBundle\Entity\Post
 *
 * @ORM\Table(name="posts_post")
 * @ORM\Entity
 * @ORM\HasLifecycleCallbacks()
 */
class Post
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
     * @var \DateTime $created_at
     *
     * @ORM\Column(name="created_at", type="datetime")
     */
    protected $created_at;

    /**
     * @var \DateTime $updated_at
     *
     * @ORM\Column(name="updated_at", type="datetime", columnDefinition="TIMESTAMP ON UPDATE CURRENT_TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP")
     */
    private $updated_at;

    /**
     * @var \Govnokod\PostsBundle\Entity\Category $category
     *
     * @ORM\ManyToOne(targetEntity="Govnokod\PostsBundle\Entity\Category", inversedBy="posts")
     * @ORM\JoinColumn(name="category_id", referencedColumnName="id")
     */
    protected $category;

    /**
     * @var \Govnokod\UserBundle\Entity\User
     *
     * @ORM\manyToOne(targetEntity="Govnokod\UserBundle\Entity\User")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id")
     */
    protected $user;

    /**
     * @var string $text
     *
     * @ORM\Column(name="body", type="text")
     */
    protected $body = '';

    /**
     * @var string $description
     *
     * @ORM\Column(name="description", type="text")
     */
    protected $description = '';

    /**
     * @var array
     *
     * @ORM\Column(name="tags", type="string", length=4096)
     */
    protected $tags_string;

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
     * @param  \DateTime $createdAt
     * @return Post
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
     * @param  string $body
     * @return Post
     */
    public function setBody($body)
    {
        $this->body = (string) $body;

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

    public function getLinesCount()
    {
        return substr_count($this->body, "\n") + 1;
    }

    public function generateLines()
    {
        $lines = array();
        $linesCount = $this->getLinesCount();

        $chars = strlen($linesCount);

        for ($i = 1; $i <= $linesCount; $i++) {
            $lines[] = sprintf('%0' . $chars . 'd', $i);
        }

        return $lines;
    }

    /**
     * Set description
     *
     * @param  string $description
     * @return Post
     */
    public function setDescription($description)
    {
        $this->description = (string) $description;

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
     * @param  \Govnokod\PostsBundle\Entity\Category $category
     * @return Post
     */
    public function setCategory(\Govnokod\PostsBundle\Entity\Category $category = null)
    {
        $this->category = $category;

        return $this;
    }

    /**
     * Get category
     *
     * @return \Govnokod\PostsBundle\Entity\Category
     */
    public function getCategory()
    {
        return $this->category;
    }

    /**
     * Set user
     *
     * @param  \Govnokod\UserBundle\Entity\User $user
     * @return Post
     */
    public function setUser(\Govnokod\UserBundle\Entity\User $user = null)
    {
        $this->user = $user;

        return $this;
    }

    /**
     * Get user
     *
     * @return \Govnokod\UserBundle\Entity\User
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * Set comments_count
     *
     * @param  integer $commentsCount
     * @return Post
     */
    public function setCommentsCount($commentsCount)
    {
        $this->comments_count = (int) $commentsCount;

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
     * @return Post
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
     * @param  float $rating
     * @return Post
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
     * @param float $by_value
     * @return Post
     */
    public function changeRating($by_value)
    {
        $this->rating += $by_value;

        return $this;
    }

    /**
     * Set votes_on
     *
     * @param  integer $votesOn
     * @return Post
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
     * @param  integer $votesAgainst
     * @return Post
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

    /**
     * Set tags_string
     *
     * @param string $tagsString
     * @return Post
     */
    public function setTagsString($tagsString)
    {
        $this->tags_string = $tagsString;

        return $this;
    }

    /**
     * Get tags_string
     *
     * @return string 
     */
    public function getTagsString()
    {
        return $this->tags_string;
    }
}
