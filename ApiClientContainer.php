<?php

namespace RunetId\ApiClientBundle;

use RunetId\ApiClientBundle\Cache\CacheInterface;
use RunetId\ApiClientBundle\Exception\ApiClientBundleException;
use Ruvents\DataReconstructor\DataReconstructor;

class ApiClientContainer
{
    /**
     * @var array
     */
    protected $options;

    /**
     * @var DataReconstructor
     */
    protected $modelReconstructor;

    /**
     * @var CacheInterface|null
     */
    protected $cache;

    /**
     * @var string
     */
    protected $currentName;

    /**
     * @var ApiCacheableClient[]
     */
    protected $clients = [];

    /**
     * @param array               $options
     * @param DataReconstructor   $modelReconstructor
     * @param CacheInterface|null $cache
     */
    public function __construct(array $options, DataReconstructor $modelReconstructor, CacheInterface $cache = null)
    {
        $this->options = $options;
        $this->modelReconstructor = $modelReconstructor;
        $this->cache = $cache;
    }

    /**
     * @param string $name
     *
     * @return ApiCacheableClient
     *
     * @throws ApiClientBundleException
     */
    public function get($name)
    {
        if (!in_array($name, $credNames = array_keys($this->options['credentials']))) {
            throw new ApiClientBundleException(sprintf(
                'Credentials profile "%s" was not found. The following are available: %s.',
                $name,
                implode(',', $credNames)
            ));
        }

        if (!isset($this->clients[$name])) {
            $this->clients[$name] = new ApiCacheableClient(
                $name,
                $this->getClientOptions($name),
                $this->modelReconstructor,
                $this->cache
            );
        }

        return $this->clients[$name];
    }

    /**
     * @return ApiCacheableClient
     */
    public function getDefault()
    {
        return $this->get($this->options['default_credentials']);
    }

    /**
     * @param string $currentName
     *
     * @return $this
     *
     * @throws ApiClientBundleException
     */
    public function setCurrentName($currentName)
    {
        if (!in_array($currentName, $credNames = array_keys($this->options['credentials']))) {
            throw new ApiClientBundleException(sprintf(
                'Credentials profile "%s" was not found. The following are available: %s.',
                $currentName,
                implode(',', $credNames)
            ));
        }

        $this->currentName = $currentName;

        return $this;
    }

    /**
     * @return ApiCacheableClient
     */
    public function getCurrent()
    {
        if (!isset($this->currentName)) {
            return $this->getDefault();
        }

        return $this->get($this->currentName);
    }

    /**
     * @param string $name
     *
     * @return array
     */
    protected function getClientOptions($name)
    {
        $options = $this->options['client'];

        $options['key'] = $this->options['credentials'][$name]['key'];
        $options['secret'] = $this->options['credentials'][$name]['secret'];

        return $options;
    }
}
