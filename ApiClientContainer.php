<?php

namespace RunetId\ApiClientBundle;

use RunetId\ApiClientBundle\Cache\CacheInterface;
use RunetId\ApiClientBundle\Exception\ApiClientBundleException;
use Ruvents\DataReconstructor\DataReconstructor;

/**
 * Class ApiClientContainer
 */
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
     * @return array
     */
    public function getOptions()
    {
        return $this->options;
    }

    /**
     * @param string $name
     * @return ApiCacheableClient
     */
    public function get($name)
    {
        if (!in_array($name, $credNames = array_keys($this->options['credentials']))) {
            throw new ApiClientBundleException(sprintf(
                '"%s" credentials set does not exist. The following are available: %s.',
                $name,
                implode(',', $credNames)
            ));
        }

        if (!isset($this->clients[$name])) {
            $options = $this->getClientOptions($name);
            $this->clients[$name] = new ApiCacheableClient($options, $this->modelReconstructor, $this->cache);
        }

        return $this->clients[$name];
    }

    /**
     * @return string
     */
    public function getDefaultName()
    {
        return $this->options['default_credentials'];
    }

    /**
     * @return ApiCacheableClient
     */
    public function getDefault()
    {
        return $this->get($this->getDefaultName());
    }

    /**
     * @param string $currentName
     * @return $this
     */
    public function setCurrentName($currentName)
    {
        $this->currentName = $currentName;

        return $this;
    }

    /**
     * @return string
     */
    public function getCurrentName()
    {
        return $this->currentName;
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
