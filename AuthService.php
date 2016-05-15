<?php

namespace RunetId\ApiClientBundle;

use Doctrine\ORM\EntityManager;
use RunetId\ApiClientBundle\Entity\AbstractUser;
use RunetId\ApiClientBundle\Entity\UserRepository;
use RunetId\ApiClientBundle\Exception\ApiClientBundleException;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorage;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;

/**
 * Class AuthService
 */
class AuthService
{
    /**
     * @var EntityManager
     */
    protected $em;

    /**
     * @var string
     */
    protected $userClass;

    /**
     * @var TokenStorage
     */
    protected $tokenStorage;

    /**
     * @var UserRepository
     */
    protected $repository;

    /**
     * @param EntityManager $em
     * @param string        $userClass
     * @param TokenStorage  $tokenStorage
     */
    public function __construct(EntityManager $em, $userClass, TokenStorage $tokenStorage)
    {
        $this->em = $em;
        $this->userClass = $userClass;
        $this->tokenStorage = $tokenStorage;

        $repository = $em->getRepository($userClass);
        if (!$repository instanceof UserRepository) {
            throw new ApiClientBundleException(sprintf(
                'Repository for entity class "%s" must extend RunetId\ApiClientBundle\Entity\UserRepository.',
                $userClass
            ));
        }

        $this->repository = $repository;
    }

    /**
     * @param int $runetId
     * @return AbstractUser
     */
    public function createUserInstance($runetId)
    {
        $userClass = $this->userClass;

        /** @var AbstractUser $user */
        $user = new $userClass($runetId);

        return $user;
    }

    /**
     * @param int $runetId
     * @return AbstractUser
     */
    public function findOrCreateUser($runetId)
    {
        $user = $this->repository
            ->findOneByRunetId($runetId);

        if (!$user) {
            $user = $this->createUserInstance($runetId);
            $this->em->persist($user);
            $this->em->flush();
        }

        return $user;
    }

    /**
     * @param AbstractUser $user
     */
    public function authUser(AbstractUser $user)
    {
        $token = new UsernamePasswordToken($user, $user->getPassword(), 'public', $user->getRoles());
        $this->tokenStorage->setToken($token);
    }
}
