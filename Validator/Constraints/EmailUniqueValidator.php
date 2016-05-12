<?php

namespace RunetId\ApiClientBundle\Validator\Constraints;

use RunetId\ApiClient\ApiClient;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

/**
 * Class EmailUniqueValidator
 */
class EmailUniqueValidator extends ConstraintValidator
{
    /**
     * @var ApiClient
     */
    protected $apiClient;

    /**
     * @param ApiClient $apiClient
     */
    public function __construct(ApiClient $apiClient)
    {
        $this->apiClient = $apiClient;
    }

    /**
     * @inheritdoc
     */
    public function validate($value, Constraint $constraint)
    {
        if (!$constraint instanceof EmailUnique) {
            throw new UnexpectedTypeException($constraint, __NAMESPACE__.'\EmailUniqueValidator');
        }

        if (count($this->apiClient->user()->search($value))) {
            $this->context
                ->buildViolation($constraint->message)
                ->addViolation();
        }
    }
}
