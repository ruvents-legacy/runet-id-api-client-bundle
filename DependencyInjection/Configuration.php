<?php

namespace RunetId\ApiClientBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * Class Configuration
 */
class Configuration implements ConfigurationInterface
{
    /**
     * @inheritdoc
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('runet_id_api_client');

        /** @noinspection PhpUndefinedMethodInspection */
        $rootNode
            ->children()
                ->arrayNode('container')
                    ->children()
                        ->arrayNode('client')
                            ->addDefaultsIfNotSet()
                            ->children()
                                ->scalarNode('host')
                                    ->defaultValue('api.runet-id.com')
                                    ->cannotBeEmpty()
                                ->end()
                                    ->booleanNode('secure')
                                    ->defaultFalse()
                                ->end()
                            ->end()
                        ->end()
                        ->scalarNode('default_credentials')
                            ->isRequired()
                        ->end()
                        ->arrayNode('credentials')
                            ->isRequired()
                            ->prototype('array')
                                ->children()
                                    ->scalarNode('key')
                                        ->isRequired()
                                    ->end()
                                    ->scalarNode('secret')
                                        ->isRequired()
                                    ->end()
                                ->end()
                            ->end()
                        ->end()
                    ->end()
                ->end()
                ->arrayNode('cache')
                    ->canBeEnabled()
                    ->children()
                        ->scalarNode('service')
                            ->defaultValue('runet_id.api_client.cache.file')
                            ->cannotBeEmpty()
                        ->end()
                        ->arrayNode('file')
                            ->addDefaultsIfNotSet()
                            ->children()
                                ->integerNode('lifetime')
                                    ->defaultValue(60)
                                ->end()
                                ->scalarNode('dir')
                                    ->defaultValue('%kernel.cache_dir%/runet_id_api_client')
                                    ->cannotBeEmpty()
                                ->end()
                            ->end()
                        ->end()
                    ->end()
                ->end()
                ->arrayNode('model_reconstructor')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->arrayNode('map')
                            ->prototype('array')
                                ->prototype('scalar')
                                    ->cannotBeEmpty()
                                ->end()
                            ->end()
                        ->end()
                        ->arrayNode('model_classes')
                            ->prototype('scalar')
                                ->cannotBeEmpty()
                            ->end()
                        ->end()
                    ->end()
                ->end()
            ->end();

        return $treeBuilder;
    }
}
