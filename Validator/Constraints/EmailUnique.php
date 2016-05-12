<?php

namespace RunetId\ApiClientBundle\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

/**
 * Class EmailUnique
 * @Annotation
 */
class EmailUnique extends Constraint
{
    /**
     * @var string
     */
    public $message = 'User with this email is already registered.';
    
    /**
     * @inheritdoc
     */
    public function validatedBy()
    {
        return 'email_unique';
    }
}
