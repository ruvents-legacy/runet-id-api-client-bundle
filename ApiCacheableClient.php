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
     * @var bool
     */
    protected $noCacheOnce = false;

    /**
     * @param array               $options
     * @param DataReconstructor   $modelReconstructor
     * @param CacheInterface|null $cache
     */
    public function __construct(array $options, DataReconstructor $modelReconstructor, CacheInterface $cache = null)
    {
        parent::__construct($options, $modelReconstructor);

        $this->cache = $cache;
    }

    /**
     * @return $this
     */
    public function noCacheOnce()
    {
        $this->noCacheOnce = true;

        return $this;
    }

    /**
     * @inheritdoc
     */
    protected function send($method, Request $request)
    {
        if (!isset($this->cache) || !$this->cache->isCacheable($request)) {
            return parent::send($method, $request);
        }

        if ($this->noCacheOnce) {
            $this->noCacheOnce = false;

            return parent::send($method, $request);
        }

        if ($response = $this->cache->read($request)) {
            return $response;
        }

        $response = parent::send($method, $request);

        $this->cache->write($request, $response);

        return $response;
    }
}
