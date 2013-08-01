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
     * @var User $sender
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
     * @param \DateTime $createdAt
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
     * @param string $ip
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
     * @param string $hash
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
     * @param string $body
     * @return Comment
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
     * Set thread
     *
     * @param \Govnokod\CommentBundle\Entity\Thread $thread
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
     * @param \Govnokod\UserBundle\Entity\User $sender
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
}