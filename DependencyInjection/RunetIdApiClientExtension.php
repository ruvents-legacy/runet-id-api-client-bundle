<?php

namespace RunetId\ApiClientBundle\DependencyInjection;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\HttpKernel\DependencyInjection\ConfigurableExtension;
use Symfony\Component\DependencyInjection\ContainerBuilder;

/**
 * Class RunetIdApiClientExtension
 */
class RunetIdApiClientExtension extends ConfigurableExtension
{
    /**
     * @inheritdoc
     */
    protected function loadInternal(array $mergedConfig, ContainerBuilder $container)
    {
        $loader = new YamlFileLoader(
            $container,
            new FileLocator(__DIR__.'/../Resources/config')
        );
        $loader->load('services.yml');

        $container->getDefinition('runet_id.api_client.model_reconstructor')
            ->replaceArgument(0, $mergedConfig['model_reconstructor']);

        $container->getDefinition('runet_id.api_client.cache.file')
            ->replaceArgument(0, $mergedConfig['cache']['file']);

        $apiContainerService = $container
            ->getDefinition('runet_id.api_client.container')
            ->replaceArgument(0, $mergedConfig['container']);
        
        $container->setParameter('runet_id.api_client.default_credentials_name', $mergedConfig['container']['default_credentials']);

        if ($mergedConfig['cache']['enabled']) {
            $cacheService = new Reference($mergedConfig['cache']['service']);
            $apiContainerService->replaceArgument(2, $cacheService);
        }
    }
}
