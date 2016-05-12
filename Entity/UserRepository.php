<?php

namespace RunetId\ApiClientBundle\Entity;

use Doctrine\ORM\EntityRepository;

/**
 * Class UserRepository
 */
class UserRepository extends EntityRepository
{
    /**
     * @param int $runetId
     * @return null|AbstractUser
     */
    public function findOneByRunetId($runetId)
    {
        return $this->findOneBy(['runetId' => $runetId]);
    }
}
