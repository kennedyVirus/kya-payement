<?php

namespace SysSecurityBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * LicenceKey
 *
 * @ORM\Table(name="licence_key")
 * @ORM\Entity(repositoryClass="SysSecurityBundle\Repository\LicenceKeyRepository")
 */
class LicenceKey
{
    /*
   * name=>licence key
   * type=>type of licence .enterprise(1),academic(2),student(3)
   * amount_category=>for 1 day(1),week(2),month(3),trimester(4),semester(5),year(6)
   * price=>licence value
   * used=>if already used by client (0 or 1)
   * delay=> duration time (in seconds)
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
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=255)
     */
    private $name;

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
     * @ORM\Column(name="price", type="string", length=255)
     */
    private $price;

    /**
     * @var int
     *
     * @ORM\Column(name="used", type="integer")
     */
    private $used;

    /**
     * @var string
     *
     * @ORM\Column(name="delay", type="string", length=255)
     */
    private $delay;

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
     * Set name
     *
     * @param string $name
     *
     * @return LicenceKey
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set type
     *
     * @param integer $type
     *
     * @return LicenceKey
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
     * @return LicenceKey
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
     * Set used
     *
     * @param integer $used
     *
     * @return LicenceKey
     */
    public function setUsed($used)
    {
        $this->used = $used;

        return $this;
    }

    /**
     * Get used
     *
     * @return int
     */
    public function getUsed()
    {
        return $this->used;
    }

    /**
     * Set delay
     *
     * @param string $delay
     *
     * @return LicenceKey
     */
    public function setDelay($delay)
    {
        $this->delay = $delay;

        return $this;
    }

    /**
     * Get delay
     *
     * @return string
     */
    public function getDelay()
    {
        return $this->delay;
    }

    /**
     * Set price
     *
     * @param string $price
     *
     * @return LicenceKey
     */
    public function setPrice($price)
    {
        $this->price = $price;

        return $this;
    }

    /**
     * Get price
     *
     * @return string
     */
    public function getPrice()
    {
        return $this->price;
    }


    /**
     * Set createdAt
     *
     * @param string $createdAt
     *
     * @return LicenceKey
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
     * @return LicenceKey
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

