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
     * @var string
     */
    private $name;

    /**
     * @var array
     */
    protected static $cacheablePaths = [
        'user/get',
        'event/info',
        'event/users',
        'professionalinterest/list',
        'section/info',
        'section/list',
        'section/user',
        'section/reports',
    ];

    /**
     * @var CacheInterface|null
     */
    protected $cache;

    /**
     * @var bool
     */
    protected $noCacheOnce = false;

    /**
     * @param string              $name
     * @param array               $options
     * @param DataReconstructor   $modelReconstructor
     * @param CacheInterface|null $cache
     */
    public function __construct(
        $name,
        array $options,
        DataReconstructor $modelReconstructor,
        CacheInterface $cache = null
    ) {
        $this->name = $name;
        parent::__construct($options, $modelReconstructor);
        $this->cache = $cache;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
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
        if (!isset($this->cache) || !$this->isRequestCacheable($request)) {
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

    /**
     * @param Request $request
     * @return bool
     */
    protected function isRequestCacheable(Request $request)
    {
        return in_array($request->getUri()->getPath(), static::$cacheablePaths, true);
    }
}
