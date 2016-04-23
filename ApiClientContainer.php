<?php

namespace RunetId\ApiClientBundle;

use RunetId\ApiClient\ApiClient;
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
     * @var ApiCache
     */
    protected $apiCache;

    /**
     * @var string
     */
    protected $currentName;

    /**
     * @var ApiClient[]
     */
    protected $clients = [];

    /**
     * @param array             $options
     * @param DataReconstructor $modelReconstructor
     * @param ApiCache          $apiCache
     */
    public function __construct(array $options, DataReconstructor $modelReconstructor, ApiCache $apiCache)
    {
        $this->options = $options;
        $this->modelReconstructor = $modelReconstructor;
        $this->apiCache = $apiCache;
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
     * @return ApiClient
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
            $this->clients[$name] = new ApiClientCacheable($options, $this->modelReconstructor, $this->apiCache);
        }

        return $this->clients[$name];
    }

    /**
     * @return ApiClient
     */
    public function getDefault()
    {
        return $this->get($this->options['default_credentials']);
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
     * @return ApiClient
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
