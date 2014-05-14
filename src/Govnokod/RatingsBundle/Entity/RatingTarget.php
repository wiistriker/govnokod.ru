<?php

namespace Govnokod\RatingsBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * RatingTarget
 *
 * @ORM\Table(name="rating_target")
 * @ORM\Entity(repositoryClass="Govnokod\RatingsBundle\Entity\RatingTargetRepository")
 */
class RatingTarget
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
    private $targetType;

    /**
     * @var string
     *
     * @ORM\Column(name="target_id", type="string", length=255)
     */
    private $targetId;

    /**
     * @var float
     *
     * @ORM\Column(name="rating", type="float")
     */
    protected $rating;

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
     * Set targetType
     *
     * @param string $targetType
     * @return RatingTarget
     */
    public function setTargetType($targetType)
    {
        $this->targetType = $targetType;

        return $this;
    }

    /**
     * Get targetType
     *
     * @return string 
     */
    public function getTargetType()
    {
        return $this->targetType;
    }

    /**
     * Set targetId
     *
     * @param string $targetId
     * @return RatingTarget
     */
    public function setTargetId($targetId)
    {
        $this->targetId = $targetId;

        return $this;
    }

    /**
     * Get targetId
     *
     * @return string 
     */
    public function getTargetId()
    {
        return $this->targetId;
    }

    /**
     * Set rating
     *
     * @param  float $rating
     * @return RatingTarget
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
     * @return RatingTarget
     */
    public function changeRating($by_value)
    {
        $this->rating += $by_value;

        return $this;
    }
}
