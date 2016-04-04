<?php

namespace RunetId\ApiClientBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * Class AbstractUser
 * @ORM\MappedSuperclass
 * @ORM\HasLifecycleCallbacks
 */
abstract class AbstractUser implements UserInterface, \Serializable
{
    /**
     * @var int
     * @ORM\Column(type="integer")
     * @ORM\Id
     */
    protected $runetId;

    /**
     * @var string
     * @ORM\Column(type="string")
     */
    protected $password;

    /**
     * @var string
     * @ORM\Column(type="string")
     */
    protected $role;

    /**
     * @var string
     * @ORM\Column(type="string", nullable=true)
     */
    protected $firstname;

    /**
     * @var string
     * @ORM\Column(type="string", nullable=true)
     */
    protected $lastname;

    /**
     * @var \DateTime
     * @ORM\Column(type="datetime")
     */
    protected $registeredAt;

    /**
     * @param int $runetId
     */
    public function __construct($runetId)
    {
        $this->runetId = $runetId;
        $this->password = base64_encode(random_bytes(50));
        $this->role = 'ROLE_USER';
    }

    /**
     * @return int
     */
    public function getRunetId()
    {
        return $this->runetId;
    }

    /**
     * @inheritdoc
     */
    public function getPassword()
    {
        return $this->password;
    }

    /**
     * @inheritdoc
     */
    public function getSalt()
    {
    }

    /**
     * @inheritdoc
     */
    public function eraseCredentials()
    {
    }

    /**
     * @param string $firstname
     * @return $this
     */
    public function setFirstname($firstname)
    {
        $this->firstname = $firstname;

        return $this;
    }

    /**
     * @return string
     */
    public function getFirstname()
    {
        return $this->firstname;
    }

    /**
     * @param string $lastname
     * @return $this
     */
    public function setLastname($lastname)
    {
        $this->lastname = $lastname;

        return $this;
    }

    /**
     * @return string
     */
    public function getLastname()
    {
        return $this->lastname;
    }

    /**
     * @return string
     */
    public function getUsername()
    {
        return trim("[$this->runetId] $this->firstname");
    }

    /**
     * @return \DateTime
     */
    public function getRegisteredAt()
    {
        return $this->registeredAt;
    }

    /**
     * @param string $role
     * @return $this
     */
    public function setRole($role)
    {
        $this->role = $role;

        return $this;
    }

    /**
     * @return string
     */
    public function getRole()
    {
        return $this->role;
    }

    /**
     * @return array
     */
    public function getRoles()
    {
        return [$this->role];
    }

    /**
     * @ORM\PrePersist
     */
    public function onPrePersist()
    {
        $this->registeredAt = new \DateTime;
    }

    /**
     * @return string
     */
    public function serialize()
    {
        return serialize([
            $this->runetId,
            $this->password,
        ]);
    }

    /**
     * @param string $serialized
     */
    public function unserialize($serialized)
    {
        list (
            $this->runetId,
            $this->password
            ) = unserialize($serialized);
    }
}
