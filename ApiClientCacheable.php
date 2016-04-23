<?php

namespace RunetId\ApiClientBundle;

use RunetId\ApiClient\ApiClient;
use Ruvents\DataReconstructor\DataReconstructor;
use Ruvents\HttpClient\Request\Request;

/**
 * Class ApiClientCacheable
 */
class ApiClientCacheable extends ApiClient
{
    /**
     * @var ApiCache
     */
    protected $apiCache;

    /**
     * @param array                  $options
     * @param DataReconstructor|null $modelReconstructor
     * @param ApiCache               $apiCache
     */
    public function __construct(array $options, DataReconstructor $modelReconstructor, ApiCache $apiCache)
    {
        parent::__construct($options, $modelReconstructor);

        $this->apiCache = $apiCache;
    }

    /**
     * @inheritdoc
     */
    protected function send($method, Request $request)
    {
        if ($response = $this->apiCache->processRequest($request)) {
            return $response;
        }

        $response = parent::send($method, $request);

        $this->apiCache->processResponse($request, $response);

        return $response;
    }
}
