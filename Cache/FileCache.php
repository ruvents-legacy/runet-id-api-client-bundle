<?php

namespace RunetId\ApiClientBundle\Cache;

use Ruvents\HttpClient\Request\Request;
use Ruvents\HttpClient\Response\Response;
use Symfony\Component\Filesystem\Filesystem;

/**
 * Class FileCache
 */
class FileCache implements CacheInterface
{
    /**
     * @var array
     */
    protected $options;

    /**
     * @var Filesystem
     */
    protected $filesystem;

    /**
     * @param array      $options
     * @param Filesystem $filesystem
     */
    public function __construct(array $options, Filesystem $filesystem)
    {
        $this->options = $options;
        $this->filesystem = $filesystem;
    }

    /**
     * @inheritdoc
     */
    public function write(Request $request, Response $response)
    {
        $filename = $this->getRequestCachePath($request);

        $this->filesystem->dumpFile($filename, $response->getRawBody());
    }

    /**
     * @inheritdoc
     */
    public function read(Request $request)
    {
        $filename = $this->getRequestCachePath($request);

        if (!$this->filesystem->exists($filename)) {
            return null;
        }

        $rawBody = file_get_contents($filename);

        return new Response($rawBody, 200, [], $request);
    }

    /**
     * @param Request $request
     * @return bool
     */
    public function isFresh(Request $request)
    {
        $filename = $this->getRequestCachePath($request);

        if (!$this->filesystem->exists($filename)) {
            return false;
        }

        return (time() - filemtime($filename)) < ($this->options['lifetime'] * 60);
    }

    public function clear()
    {
        $this->filesystem->remove($this->options['dir']);
    }

    /**
     * @param Request $request
     * @return string
     */
    protected function getRequestCachePath(Request $request)
    {
        $hash = $this->getRequestHash($request);

        return $this->options['dir'].'/'.rtrim(chunk_split($hash, 8, '/'), '/');
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
