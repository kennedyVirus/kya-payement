<?php

namespace SysSecurityBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Verification
 *
 * @ORM\Table(name="verification")
 * @ORM\Entity(repositoryClass="SysSecurityBundle\Repository\VerificationRepository")
 */
class Verification
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="phone_number", type="string", length=255, nullable=true)
     */
    private $phoneNumber;

    /**
     * @var string
     *
     * @ORM\Column(name="email", type="string", length=255, nullable=true)
     */
    private $email;

    /**
     * @var string
     *
     * @ORM\Column(name="request_id", type="string", length=255,nullable=true)
     */
    private $requestId;

    /**
     * @var string
     *
     * @ORM\Column(name="transaction_code", type="string", length=255)
     */
    private $transactionCode;

    /**
     * @var string
     *
     * @ORM\Column(name="code", type="string", length=255,nullable=true)
     */
    private $code;

    /**
     * @var int
     *
     * @ORM\Column(name="licence_key_id", type="integer")
     */
    private $licenceKeyId;

    /**
     * @var int
     *
     * @ORM\Column(name="state", type="integer")
     */
    private $state;

    /**
     * @var string
     *
     * @ORM\Column(name="created_at", type="string", length=255)
     */
    private $createdAt;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="updated_at", type="datetime")
     */
    private $updatedAt;



    /**
     * Get id
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set phoneNumber
     *
     * @param string $phoneNumber
     *
     * @return Verification
     */
    public function setPhoneNumber($phoneNumber)
    {
        $this->phoneNumber = $phoneNumber;

        return $this;
    }

    /**
     * Get phoneNumber
     *
     * @return string
     */
    public function getPhoneNumber()
    {
        return $this->phoneNumber;
    }

    /**
     * Set email
     *
     * @param string $email
     *
     * @return Verification
     */
    public function setEmail($email)
    {
        $this->email = $email;

        return $this;
    }

    /**
     * Get email
     *
     * @return string
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * Set requestId
     *
     * @param string $requestId
     *
     * @return Verification
     */
    public function setRequestId($requestId)
    {
        $this->requestId = $requestId;

        return $this;
    }

    /**
     * Get requestId
     *
     * @return string
     */
    public function getRequestId()
    {
        return $this->requestId;
    }

    /**
     * Set transactionCode
     *
     * @param string $transactionCode
     *
     * @return Verification
     */
    public function setTransactionCode($transactionCode)
    {
        $this->transactionCode = $transactionCode;

        return $this;
    }

    /**
     * Get transactionCode
     *
     * @return string
     */
    public function getTransactionCode()
    {
        return $this->transactionCode;
    }

    /**
     * Set code
     *
     * @param string $code
     *
     * @return Verification
     */
    public function setCode($code)
    {
        $this->code = $code;

        return $this;
    }

    /**
     * Get code
     *
     * @return string
     */
    public function getCode()
    {
        return $this->code;
    }

    /**
     * Set licenceKeyId
     *
     * @param integer $licenceKeyId
     *
     * @return Verification
     */
    public function setLicenceKeyId($licenceKeyId)
    {
        $this->licenceKeyId = $licenceKeyId;

        return $this;
    }

    /**
     * Get licenceKeyId
     *
     * @return int
     */
    public function getLicenceKeyId()
    {
        return $this->licenceKeyId;
    }

    /**
     * Set state
     *
     * @param integer $state
     *
     * @return Verification
     */
    public function setState($state)
    {
        $this->state = $state;

        return $this;
    }

    /**
     * Get state
     *
     * @return int
     */
    public function getState()
    {
        return $this->state;
    }

    /**
     * Set createdAt
     *
     * @param string $createdAt
     *
     * @return Verification
     */
    public function setCreatedAt($createdAt)
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    /**
     * Get createdAt
     *
     * @return string
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * Set updatedAt
     *
     * @param \DateTime $updatedAt
     *
     * @return Verification
     */
    public function setUpdatedAt($updatedAt)
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    /**
     * Get updatedAt
     *
     * @return \DateTime
     */
    public function getUpdatedAt()
    {
        return $this->updatedAt;
    }
}

