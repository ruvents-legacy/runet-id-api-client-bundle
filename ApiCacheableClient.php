<?php

namespace RunetId\ApiClientBundle;

use RunetId\ApiClient\ApiClient;
use RunetId\ApiClientBundle\Cache\CacheInterface;
use Ruvents\DataReconstructor\DataReconstructor;
use Ruvents\HttpClient\Request\Request;

/**
 * Class ApiCacheableClient
 */
class ApiCacheableClient extends ApiClient
{
    /**
     * @var CacheInterface|null
     */
    protected $cache;

    /**
     * @param array                  $options
     * @param DataReconstructor $modelReconstructor
     * @param CacheInterface|null         $cache
     */
    public function __construct(array $options, DataReconstructor $modelReconstructor, CacheInterface $cache = null)
    {
        parent::__construct($options, $modelReconstructor);

        $this->cache = $cache;
    }

    /**
     * @inheritdoc
     */
    protected function send($method, Request $request)
    {
        if (!isset($this->cache)) {
            return parent::send($method, $request);
        }
        
        if ($response = $this->cache->processRequest($request)) {
            return $response;
        }

        $response = parent::send($method, $request);

        $this->cache->processResponse($request, $response);

        return $response;
    }
}
