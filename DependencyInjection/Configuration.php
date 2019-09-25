<?php

declare(strict_types=1);

namespace Mews\PosBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class Configuration implements ConfigurationInterface
{
    /**
     * Generates the configuration tree builder.
     *
     * @return TreeBuilder The tree builder
     */
    public function getConfigTreeBuilder(): TreeBuilder
    {
        $treeBuilder = new TreeBuilder('pos');

        if (method_exists($treeBuilder, 'getRootNode')) {
            $rootNode = $treeBuilder->getRootNode();
        } else {
            // BC layer for symfony/config 4.1 and older
            $rootNode = $treeBuilder->root('pos');
        }

        $rootNode
            ->children()
                ->arrayNode('currencies')
                    ->scalarPrototype()->end()
                ->end()
                ->arrayNode('banks')
                    ->arrayPrototype()
                        ->children()
                            ->scalarNode('name')->isRequired()->end()
                            ->scalarNode('class')->isRequired()->end()
                            ->arrayNode('urls')->isRequired()
                                ->children()
                                    ->scalarNode('production')->isRequired()->end()
                                    ->scalarNode('test')->isRequired()->end()
                                    ->arrayNode('gateway')->isRequired()
                                        ->children()
                                            ->scalarNode('production')->isRequired()->end()
                                            ->scalarNode('test')->isRequired()->end()
                                        ->end()
                                    ->end()
                                ->end()
                            ->end()
                        ->end()
                    ->end()
                ->end()
            ->end();

        return $treeBuilder;
    }
}
