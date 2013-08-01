<?php

namespace Govnokod\CommentBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Thread
 *
 * @ORM\Table(name="comment_thread")
 * @ORM\Entity(repositoryClass="Govnokod\CommentBundle\Entity\ThreadRepository")
 */
class Thread
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="target_type", type="string", length=255)
     */
    private $target_type;

    /**
     * @var integer
     *
     * @ORM\Column(name="target_id", type="integer")
     */
    private $target_id;

    /**
     * @var integer
     *
     * @ORM\Column(name="comments_count", type="integer")
     */
    private $comments_count = 0;

    /**
     * @ORM\OneToMany(targetEntity="Govnokod\CommentBundle\Entity\Comment", mappedBy="thread", cascade={"persist", "remove"})
     * @ORM\OrderBy({"id" = "ASC"})
     */
    protected $comments;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->comments = new \Doctrine\Common\Collections\ArrayCollection();
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
     * Set target_type
     *
     * @param string $targetType
     * @return Thread
     */
    public function setTargetType($targetType)
    {
        $this->target_type = $targetType;

        return $this;
    }

    /**
     * Get target_type
     *
     * @return string
     */
    public function getTargetType()
    {
        return $this->target_type;
    }

    /**
     * Set target_id
     *
     * @param integer $targetId
     * @return Thread
     */
    public function setTargetId($targetId)
    {
        $this->target_id = $targetId;

        return $this;
    }

    /**
     * Get target_id
     *
     * @return integer
     */
    public function getTargetId()
    {
        return $this->target_id;
    }

    /**
     * Add comments
     *
     * @param \Govnokod\CommentBundle\Entity\Comment $comment
     * @return Thread
     */
    public function addComment(\Govnokod\CommentBundle\Entity\Comment $comment)
    {
        $comment->setThread($this);
        $this->comments[] = $comment;
        $this->comments_count++;

        return $this;
    }

    /**
     * Remove comments
     *
     * @param \Govnokod\CommentBundle\Entity\Comment $comment
     */
    public function removeComment(\Govnokod\CommentBundle\Entity\Comment $comment)
    {
        $this->comments->removeElement($comment);
    }

    /**
     * Get comments
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getComments()
    {
        return $this->comments;
    }

    /**
     * Set comments_count
     *
     * @param integer $commentsCount
     * @return Thread
     */
    public function setCommentsCount($commentsCount)
    {
        $this->comments_count = $commentsCount;

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
}