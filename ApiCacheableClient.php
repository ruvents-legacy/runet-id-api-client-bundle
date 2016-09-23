<?php

namespace RunetId\ApiClientBundle;

use RunetId\ApiClient\ApiClient;
use RunetId\ApiClientBundle\Cache\CacheInterface;
use Ruvents\DataReconstructor\DataReconstructor;
use Ruvents\HttpClient\Request\Request;
use Ruvents\HttpClient\Response\Response;

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
     * @var bool
     */
    protected $useCache = true;

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
     * @param bool $useCache
     * @return $this
     */
    public function setUseCache($useCache)
    {
        $this->useCache = $useCache;

        return $this;
    }

    /**
     * @return bool
     */
    public function getUseCache()
    {
        return $this->useCache;
    }

    /**
     * @inheritdoc
     */
    protected function send($method, Request $request)
    {
        if (!isset($this->cache) || !$this->isRequestCacheable($request)) {
            return parent::send($method, $request);
        }

        switch (true) {
            /** @noinspection PhpMissingBreakStatementInspection */
            case $this->noCacheOnce:
                $this->noCacheOnce = false;
            case !$this->useCache:
            case !$this->cache->isFresh($request):
                return $this->safeSend($method, $request);

            default:
                return $this->cache->read($request);
        }
    }

    /**
     * @param string  $method
     * @param Request $request
     * @return Response|null
     * @throws \RuntimeException
     */
    protected function safeSend($method, Request $request)
    {
        try {
            $response = parent::send($method, $request);

            if ($response->getCode() >= 400) {
                throw new \RuntimeException();
            }

            $this->cache->write($request, $response);

            return $response;
        } catch (\RuntimeException $e) {
            $response = $this->cache->read($request);

            if (!$response) {
                throw $e;
            }

            return $response;
        }
    }

    /**
     * @param Request $request
     * @return bool
     */
    protected function isRequestCacheable(Request $request)
    {
        return in_array(ltrim($request->getUri()->getPath(), '/'), static::$cacheablePaths, true);
    }
}
