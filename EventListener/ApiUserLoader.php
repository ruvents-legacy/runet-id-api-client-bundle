<?php

namespace RunetId\ApiClientBundle\EventListener;

use Doctrine\ORM\Event\LifecycleEventArgs;
use RunetId\ApiClient\ApiClient;
use RunetId\ApiClientBundle\ApiUserProxy;

/**
 * Class ApiUserLoader
 */
class ApiUserLoader
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
     * @param LifecycleEventArgs $args
     */
    public function postLoad(LifecycleEventArgs $args)
    {
        $entity = $args->getEntity();

        if (!$entity instanceof ApiUserProxy) {
            return;
        }

        $apiData = $this->apiClient
            ->user($entity->getRunetId())
            ->get();

        $entity->setApiData($apiData);
    }
}
