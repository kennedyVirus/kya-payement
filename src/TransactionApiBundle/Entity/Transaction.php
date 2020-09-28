<?php

namespace TransactionApiBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Transaction
 *
 * @ORM\Table(name="transaction")
 * @ORM\Entity(repositoryClass="TransactionApiBundle\Repository\TransactionRepository")
 */
class Transaction
{
    /*
     * ClientId=>user id
     * state=>pending(0),success(1),failed(-1)
     * paymentMode=>tmoney(1),flooz(2),card(3)
     * provider=>paygate or paydunya
     * amount=>amount payed
     * type=>type of licence .enterprise(1),academic(2),student(3)
     * amount_category=>for 1 day(1),week(2),month(3),trimester(4),semester(5),year(6)
     * username=> can be phone_number or email
     * details=>details of transaction (pay of KYA SOL DESIGN)
     * created_at=>date of creation in timestamp
     * updated_at=>
     */

    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;


    /**
     * @var int
     *
     * @ORM\Column(name="client_id", type="integer")
     */
    private $clientId;

    /**
     * @var int
     *
     * @ORM\Column(name="state", type="integer")
     */
    private $state;

    /**
     * @var int
     *
     * @ORM\Column(name="payment_mode", type="integer")
     */
    private $paymentMode;

    /**
     * @var string
     *
     * @ORM\Column(name="provider", type="string", length=255)
     */
    private $provider;

    /**
     * @var string
     *
     * @ORM\Column(name="amount", type="string", length=255)
     */
    private $amount;

    /**
     * @var int
     *
     * @ORM\Column(name="type", type="integer")
     */
    private $type;

    /**
     * @var int
     *
     * @ORM\Column(name="amount_category", type="integer")
     */
    private $amountCategory;

    /**
     * @var string
     *
     * @ORM\Column(name="username", type="string", length=255)
     */
    private $username;

    /**
     * @var string
     *
     * @ORM\Column(name="details", type="string", length=255)
     */
    private $details;



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
     * Set clientId
     *
     * @param integer $clientId
     *
     * @return Transaction
     */
    public function setClientId($clientId)
    {
        $this->clientId = $clientId;

        return $this;
    }

    /**
     * Get clientId
     *
     * @return int
     */
    public function getClientId()
    {
        return $this->clientId;
    }

    /**
     * Set state
     *
     * @param integer $state
     *
     * @return Transaction
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
     * Set paymentMode
     *
     * @param integer $paymentMode
     *
     * @return Transaction
     */
    public function setPaymentMode($paymentMode)
    {
        $this->paymentMode = $paymentMode;

        return $this;
    }

    /**
     * Get paymentMode
     *
     * @return int
     */
    public function getPaymentMode()
    {
        return $this->paymentMode;
    }

    /**
     * Set provider
     *
     * @param string $provider
     *
     * @return Transaction
     */
    public function setProvider($provider)
    {
        $this->provider = $provider;

        return $this;
    }

    /**
     * Get provider
     *
     * @return string
     */
    public function getProvider()
    {
        return $this->provider;
    }

    /**
     * Set amount
     *
     * @param string $amount
     *
     * @return Transaction
     */
    public function setAmount($amount)
    {
        $this->amount = $amount;

        return $this;
    }

    /**
     * Get amount
     *
     * @return string
     */
    public function getAmount()
    {
        return $this->amount;
    }

    /**
     * Set type
     *
     * @param integer $type
     *
     * @return Transaction
     */
    public function setType($type)
    {
        $this->type = $type;

        return $this;
    }

    /**
     * Get type
     *
     * @return int
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Set amountCategory
     *
     * @param integer $amountCategory
     *
     * @return Transaction
     */
    public function setAmountCategory($amountCategory)
    {
        $this->amountCategory = $amountCategory;

        return $this;
    }

    /**
     * Get amountCategory
     *
     * @return int
     */
    public function getAmountCategory()
    {
        return $this->amountCategory;
    }

    /**
     * Set username
     *
     * @param string $username
     *
     * @return Transaction
     */
    public function setUsername($username)
    {
        $this->username = $username;

        return $this;
    }

    /**
     * Get username
     *
     * @return string
     */
    public function getUsername()
    {
        return $this->username;
    }


    /**
     * Set details
     *
     * @param string $details
     *
     * @return Transaction
     */
    public function setDetails($details)
    {
        $this->details = $details;

        return $this;
    }

    /**
     * Get details
     *
     * @return string
     */
    public function getDetails()
    {
        return $this->details;
    }

    /**
     * Set createdAt
     *
     * @param string $createdAt
     *
     * @return Transaction
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
     * @return Transaction
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

