<?php

namespace Govnokod\CommentBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * Comment
 *
 * @ORM\Table(name="comment")
 * @ORM\Entity(repositoryClass="Govnokod\CommentBundle\Entity\CommentRepository")
 * @Gedmo\Tree(type="materializedPath")
 * @ORM\HasLifecycleCallbacks
 */
class Comment
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @Gedmo\TreePathSource
     */
    private $id;

    /**
     * @Gedmo\TreePath
     * @ORM\Column(name="path", type="string", length=3000, nullable=true)
     */
    private $path;

    /**
     * @Gedmo\TreeParent
     * @ORM\ManyToOne(targetEntity="Comment", inversedBy="children")
     * @ORM\JoinColumns({
     *     @ORM\JoinColumn(name="parent_id", referencedColumnName="id", onDelete="CASCADE")
     * })
     */
    private $parent;

    /**
     * @Gedmo\TreeLevel
     * @ORM\Column(name="lvl", type="integer", nullable=true)
     */
    private $level;

    /**
     * @ORM\OneToMany(targetEntity="Comment", mappedBy="parent")
     */
    private $children;

    /**
     * @ORM\ManyToOne(targetEntity="Govnokod\CommentBundle\Entity\Thread", inversedBy="comments")
     * @ORM\JoinColumn(name="thread_id", referencedColumnName="id")
     */
    private $thread;

    /**
     * @var \Govnodkoe\UserBundle\Entity\User $sender
     *
     * @ORM\ManyToOne(targetEntity="Govnokod\UserBundle\Entity\User")
     * @ORM\JoinColumn(name="sender_id", referencedColumnName="id")
     *
     **/
    private $sender;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="created_at", type="datetime")
     */
    private $created_at;

    /**
     * @var string
     *
     * @ORM\Column(name="ip", type="string", length=32)
     */
    private $ip = '';

    /**
     * @var string
     *
     * @ORM\Column(name="hash", type="string", length=32)
     */
    private $hash = '';

    /**
     * @var string
     *
     * @ORM\Column(name="body", type="text")
     */
    private $body = '';

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
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    public function setParent(Comment $parent = null)
    {
        $this->parent = $parent;
    }

    public function getParent()
    {
        return $this->parent;
    }

    public function setPath($path)
    {
        $this->path = $path;
    }

    public function getPath()
    {
        return $this->path;
    }

    public function getLevel()
    {
        return $this->level;
    }

    /**
     * Set created_at
     *
     * @param  \DateTime $createdAt
     * @return Comment
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
     * Set ip
     *
     * @param  string  $ip
     * @return Comment
     */
    public function setIp($ip)
    {
        $this->ip = $ip;

        return $this;
    }

    /**
     * Get ip
     *
     * @return string
     */
    public function getIp()
    {
        return $this->ip;
    }

    /**
     * Set hash
     *
     * @param  string  $hash
     * @return Comment
     */
    public function setHash($hash)
    {
        $this->hash = $hash;

        return $this;
    }

    /**
     * Get hash
     *
     * @return string
     */
    public function getHash()
    {
        return $this->hash;
    }

    /**
     * Set body
     *
     * @param  string  $body
     * @return Comment
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

    /**
     * Set thread
     *
     * @param  \Govnokod\CommentBundle\Entity\Thread $thread
     * @return Comment
     */
    public function setThread(\Govnokod\CommentBundle\Entity\Thread $thread = null)
    {
        $this->thread = $thread;

        return $this;
    }

    /**
     * Get thread
     *
     * @return \Govnokod\CommentBundle\Entity\Thread
     */
    public function getThread()
    {
        return $this->thread;
    }

    /**
     * Set sender
     *
     * @param  \Govnokod\UserBundle\Entity\User $sender
     * @return Comment
     */
    public function setSender(\Govnokod\UserBundle\Entity\User $sender = null)
    {
        $this->sender = $sender;

        return $this;
    }

    /**
     * Get sender
     *
     * @return \Govnokod\UserBundle\Entity\User
     */
    public function getSender()
    {
        return $this->sender;
    }

    /**
     * @ORM\PrePersist
     */
    public function prePersist()
    {
        if (!$this->created_at) {
            $this->created_at = new \DateTime();
        }

        if (!$this->hash) {
            $this->hash = md5(microtime(true) . mt_rand(-10000, 10000));
        }
    }

    /**
     * Set rating
     *
     * @param  float $rating
     * @return Comment
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
     * @return Comment
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
     * @return Comment
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
     * @return Comment
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
