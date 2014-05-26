<?php

namespace Govnokod\UserBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * UserSignup
 *
 * @ORM\Table(name="user_signup")
 * @ORM\Entity(repositoryClass="Govnokod\UserBundle\Entity\UserSignupRepository")
 * @ORM\HasLifecycleCallbacks()
 */
class UserSignup
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
     * @ORM\Column(name="createdAt", type="datetime")
     */
    private $createdAt;

    /**
     * @var string
     *
     * @ORM\Column(name="service", type="string", length=255)
     */
    private $service;

    /**
     * @var string
     *
     * @ORM\Column(name="service_user_id", type="string", length=255)
     */
    private $serviceUserId;

    /**
     * @var integer
     *
     * @ORM\Column(name="user_id", type="integer")
     */
    private $userId;


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
     * @return UserSignup
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
     * Set service
     *
     * @param string $service
     * @return UserSignup
     */
    public function setService($service)
    {
        $this->service = $service;

        return $this;
    }

    /**
     * Get service
     *
     * @return string 
     */
    public function getService()
    {
        return $this->service;
    }

    /**
     * Set serviceUserId
     *
     * @param string $serviceUserId
     * @return UserSignup
     */
    public function setServiceUserId($serviceUserId)
    {
        $this->serviceUserId = $serviceUserId;

        return $this;
    }

    /**
     * Get serviceUserId
     *
     * @return string 
     */
    public function getServiceUserId()
    {
        return $this->serviceUserId;
    }

    /**
     * Set userId
     *
     * @param integer $userId
     * @return UserSignup
     */
    public function setUserId($userId)
    {
        $this->userId = $userId;

        return $this;
    }

    /**
     * Get userId
     *
     * @return integer 
     */
    public function getUserId()
    {
        return $this->userId;
    }

    /**
     * @ORM\PrePersist
     */
    public function prePersist()
    {
        if (!$this->createdAt) {
            $this->createdAt = new \DateTime();
        }
    }
}
