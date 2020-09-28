<?php

namespace SysSecurityBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * ClientLogin
 *
 * @ORM\Table(name="client_login")
 * @ORM\Entity(repositoryClass="SysSecurityBundle\Repository\ClientLoginRepository")
 */
class ClientLogin
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
     * @var int
     *
     * @ORM\Column(name="client_id", type="integer")
     */
    private $clientId;

    /**
     * @var string
     *
     * @ORM\Column(name="mac_address", type="string", length=255)
     */
    private $macAddress;

    /**
     * @var string
     *
     * @ORM\Column(name="ip_address", type="string", length=255, nullable=true)
     */
    private $ipAddress;

    /**
     * @var int
     *
     * @ORM\Column(name="licence_key_id", type="integer", length=255)
     */
    private $licenceKeyId;

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
     * @return ClientLogin
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
     * Set macAddress
     *
     * @param string $macAddress
     *
     * @return ClientLogin
     */
    public function setMacAddress($macAddress)
    {
        $this->macAddress = $macAddress;

        return $this;
    }

    /**
     * Get macAddress
     *
     * @return string
     */
    public function getMacAddress()
    {
        return $this->macAddress;
    }

    /**
     * Set ipAddress
     *
     * @param string $ipAddress
     *
     * @return ClientLogin
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
     * Set licenceKeyId
     *
     * @param integer $licenceKeyId
     *
     * @return ClientLogin
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
     * Set createdAt
     *
     * @param string $createdAt
     *
     * @return ClientLogin
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
     * @return ClientLogin
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

