<?php

namespace RunetId\ApiClientBundle;

use RunetId\ApiClient\ApiClient;
use RunetId\ApiClientBundle\Exception\ApiClientBundleException;

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
     * @var string
     */
    protected $currentName;

    /**
     * @var ApiClient[]
     */
    protected $clients = [];

    /**
     * @param array $options
     */
    public function __construct(array $options)
    {
        $this->options = $options;
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
            $this->clients[$name] = new ApiClient($options);
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
     * @param string $clientName
     * @return array
     */
    protected function getClientOptions($clientName)
    {
        $options = [];

        foreach (['host', 'secure', 'model_reconstructor'] as $optionName) {
            $options[$optionName] = $this->options[$optionName];
        }

        $options['key'] = $this->options['credentials'][$clientName]['key'];
        $options['secret'] = $this->options['credentials'][$clientName]['secret'];

        return $options;
    }
}
