<?php

namespace RunetId\ApiClientBundle\Twig;

use RunetId\ApiClient\Model\User\Status;
use RunetId\ApiClientBundle\Exception\ApiClientBundleException;

/**
 * Class ApiExtension
 */
class ApiExtension extends \Twig_Extension
{
    /**
     * @inheritdoc
     */
    public function getFunctions()
    {
        return [
            new \Twig_SimpleFunction('api_role', function ($name) {
                $constName = Status::class.'::'.'ROLE_'.strtoupper($name);

                if (!defined($constName)) {
                    throw new ApiClientBundleException(sprintf(
                        '"%s" is not found.',
                        $constName
                    ));
                }

                return constant($constName);
            }),
        ];
    }

    /**
     * @inheritdoc
     */
    public function getName()
    {
        return 'runet_id_api_extension';
    }
}
