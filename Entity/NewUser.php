<?php

namespace RunetId\ApiClientBundle\Entity;

use Symfony\Component\Validator\Constraints as Assert;
use RunetId\ApiClientBundle\Validator\Constraints as RunetIdAssert;

/**
 * Class NewUser
 */
class NewUser
{
    /**
     * @var string
     * @Assert\NotBlank
     * @RunetIdAssert\EmailUnique
     */
    public $Email;

    /**
     * @var string
     * @Assert\NotBlank
     */
    public $LastName;

    /**
     * @var string
     * @Assert\NotBlank
     */
    public $FirstName;

    /**
     * @var string
     */
    public $FatherName;

    /**
     * @var string
     * @Assert\NotBlank
     */
    public $Phone;

    /**
     * @var string
     */
    public $Company;

    /**
     * @var string
     */
    public $Position;
}
