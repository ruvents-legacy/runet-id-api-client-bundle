<?php

namespace RunetId\ApiClientBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use RunetId\ApiClientBundle\ApiUserProxy;
use Symfony\Component\Security\Core\User\UserInterface;
use RunetId\ApiClient\Model\User as ApiUser;

/**
 * Class AbstractUser
 *
 * @ORM\MappedSuperclass(repositoryClass="RunetId\ApiClientBundle\Entity\UserRepository")
 * @ORM\Table(uniqueConstraints={
 *     @ORM\UniqueConstraint(name="runetId", columns={"runet_id"})
 * })
 * @ORM\HasLifecycleCallbacks
 */
abstract class AbstractUser extends ApiUserProxy implements UserInterface, \Serializable
{
    /**
     * @var int
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @var int
     * @ORM\Column(type="integer")
     */
    protected $runetId;

    /**
     * @var string
     * @ORM\Column(type="string")
     */
    private $password;

    /**
     * @var string
     * @ORM\Column(type="string")
     */
    protected $roles;

    /**
     * @var \DateTime
     * @ORM\Column(type="datetime")
     */
    protected $registeredAt;

    /**
     * @return string
     */
    public function __toString()
    {
        return trim("$this->firstName $this->lastName");
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param int $runetId
     * @return $this
     */
    public function setRunetId($runetId)
    {
        $this->runetId = $runetId;

        return $this;
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
    public function getUsername()
    {
        return $this->firstName;
    }

    /**
     * @return string
     */
    public function getUrl()
    {
        return ApiUser::getUrlByRunetId($this->runetId);
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
     * @param array $roles
     * @return $this
     */
    public function setRole(array $roles)
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getRoles()
    {
        return $this->roles;
    }

    /**
     * @return \DateTime
     */
    public function getRegisteredAt()
    {
        return $this->registeredAt;
    }

    /**
     * @inheritdoc
     */
    public function eraseCredentials()
    {
    }

    /**
     * @ORM\PrePersist
     */
    public function onPrePersist()
    {
        $this->password = base64_encode(random_bytes(10));
        $this->registeredAt = new \DateTime;
    }

    /**
     * @inheritdoc
     */
    public function serialize()
    {
        return serialize([
            $this->id,
            $this->runetId,
            $this->password,
        ]);
    }

    /**
     * @inheritdoc
     */
    public function unserialize($serialized)
    {
        list (
            $this->id,
            $this->runetId,
            $this->password
            ) = unserialize($serialized);
    }
}
