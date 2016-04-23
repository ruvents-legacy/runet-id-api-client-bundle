<?php

namespace RunetId\ApiClientBundle;

use Ruvents\HttpClient\HttpClient;
use Ruvents\HttpClient\Request\Request;
use Ruvents\HttpClient\Response\Response;
use Symfony\Component\Filesystem\Exception\IOException;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class ApiClient
 */
class ApiClient extends \RunetId\ApiClient\ApiClient
{
    /**
     * @var array
     */
    protected static $cacheSupportedPaths = [
        'user/get',
    ];

    /**
     * @var Filesystem
     */
    protected $filesystem;

    /**
     * @inheritdoc
     */
    public function __construct(array $options = [])
    {
        parent::__construct($options);
        $this->filesystem = new Filesystem();
    }

    /**
     * @inheritdoc
     */
    public function get($path, array $data = [], array $headers = [])
    {
        if (!$this->isCacheSupported($path)) {
            return parent::get($path, $data, $headers);
        }

        $request = $this->createRequest($path, $data, [], $headers);

        return $this->processCachedResponse($request, 'get');
    }

    /**
     * @inheritdoc
     */
    public function post($path, array $query = [], $data = null, array $headers = [], array $files = [])
    {
        if (!$this->isCacheSupported($path)) {
            return parent::post($path, $query, $data, $headers, $files);
        }

        $request = $this->createRequest($path, $query, $data, $headers, $files);

        return $this->processCachedResponse($request, 'post');
    }

    /**
     * @inheritdoc
     */
    protected function configureOptions(OptionsResolver $resolver)
    {
        parent::configureOptions($resolver);

        $resolver
            ->setDefaults([
                'use_cache' => false,
                'cache_lifetime' => null,
                'cache_dir' => null,
            ])
            ->setRequired(['use_cache', 'cache_lifetime', 'cache_dir'])
            ->setAllowedTypes('cache_lifetime', 'int')
            ->setAllowedTypes('cache_dir', 'string');
    }

    /**
     * @param string $path
     * @return bool
     */
    protected function isCacheSupported($path)
    {
        return $this->options['use_cache'] && in_array($path, self::$cacheSupportedPaths);
    }

    /**
     * @param Request $request
     * @param string  $method
     * @return Response
     */
    protected function processCachedResponse(Request $request, $method)
    {
        if (!$response = $this->readCache($request)) {
            $response = HttpClient::$method($request);
            $this->writeCache($request, $response);
        }

        return $response;
    }

    /**
     * @param Request  $request
     * @param Response $response
     */
    protected function writeCache(Request $request, Response $response)
    {
        $filename = $this->getRequestCachePath($request);

        $this->filesystem->dumpFile($filename, $response->getRawBody());
    }

    /**
     * @param Request $request
     * @return null|Response
     */
    protected function readCache(Request $request)
    {
        $filename = $this->getRequestCachePath($request);

        if ($this->isCacheFresh($request)) {
            $rawBody = file_get_contents($filename);

            return new Response($rawBody, 200, [], $request);
        }

        return null;
    }

    /**
     * @param Request $request
     * @return bool
     */
    protected function isCacheFresh(Request $request)
    {
        $filename = $this->getRequestCachePath($request);

        if (!$this->filesystem->exists($filename)) {
            return false;
        }

        return (time() - filemtime($filename)) < ($this->options['cache_lifetime'] * 60);
    }

    /**
     * @throws IOException
     */
    public function clearCache()
    {
        $this->filesystem->remove($this->options['cache_dir']);
    }

    /**
     * @param Request $request
     * @return string
     */
    protected function getRequestCachePath(Request $request)
    {
        $hash = $this->getRequestHash($request);

        return $this->options['cache_dir'].'/'.rtrim(chunk_split($hash, 8, '/'), '/');
    }

    /**
     * @param Request $request
     * @return string
     */
    protected function getRequestHash(Request $request)
    {
        $uri = clone $request->getUri();
        $uri->addQueryParams($request->getData());

        return md5($uri->buildUri());
    }
}
