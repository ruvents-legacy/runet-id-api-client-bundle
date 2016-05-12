<?php

namespace RunetId\ApiClientBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Templating\EngineInterface;

/**
 * Class AuthController
 */
class AuthController
{
    /**
     * @var EngineInterface
     */
    protected $templating;

    /**
     * @param EngineInterface $templating
     */
    public function __construct(EngineInterface $templating)
    {
        $this->templating = $templating;
    }

    public function registerAction()
    {
    }

    public function tokenAction()
    {
    }
}
