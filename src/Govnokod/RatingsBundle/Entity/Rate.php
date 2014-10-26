<?php

namespace Govnokod\RatingsBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Rate
 *
 * @ORM\Table(name="rating_rate")
 * @ORM\Entity(repositoryClass="Govnokod\RatingsBundle\Entity\RateRepository")
 * @ORM\HasLifecycleCallbacks
 */
class Rate
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
     * @var \DateTime
     *
     * @ORM\Column(name="createdAt", type="datetimetz")
     */
    private $createdAt;

    /**
     * @var \Govnokod\UserBundle\Entity\User $user
     *
     * @ORM\ManyToOne(targetEntity="Govnokod\UserBundle\Entity\User")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id")
     *
     **/
    private $user;

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
     * @var string
     *
     * @ORM\Column(name="ip_address", type="string", length=50)
     */
    private $ipAddress;

    /**
     * @var float
     *
     * @ORM\Column(name="value", type="float")
     */
    private $value;

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
     * Set createdAt
     *
     * @param \DateTime $createdAt
     * @return Rate
     */
    public function setCreatedAt($createdAt)
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    /**
     * Get createdAt
     *
     * @return \DateTime 
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * Set user
     *
     * @param \Govnokod\UserBundle\Entity\User $user
     * @return Rate
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
     * Set ipAddress
     *
     * @param string $ipAddress
     * @return Rate
     */
    public function setIpAddress($ipAddress)
    {
        $this->ipAddress = $ipAddress;

        return $this;
    }

    /**
     * Get ipAddress
     *
     * @return string 
     */
    public function getIpAddress()
    {
        return $this->ipAddress;
    }

    /**
     * Set value
     *
     * @param float $value
     * @return Rate
     */
    public function setValue($value)
    {
        $this->value = $value;

        return $this;
    }

    /**
     * Get value
     *
     * @return float 
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * Set targetType
     *
     * @param string $targetType
     * @return Rate
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
     * @return Rate
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

    /** @ORM\PrePersist */
    public function prePersist()
    {
        if (!$this->createdAt) {
            $this->createdAt = new \DateTime();
        }
    }
}
