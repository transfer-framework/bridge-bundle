<?php

/*
 * This file is part of Transfer.
 *
 * For the full copyright and license information, please view the LICENSE file located
 * in the root directory.
 */

namespace Bridge\Bundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class Configuration implements ConfigurationInterface
{
    /**
     * {@inheritdoc}
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('bridge');

        $rootNode
            ->children()
                ->arrayNode('services')
                    ->info('List of services')
                    ->useAttributeAsKey('name')
                    ->prototype('array')
                        ->info('Service definition')
                        ->beforeNormalization()
                            ->ifTrue(function ($value) {
                                return !array_key_exists('groups', $value);
                            })
                            ->then(function ($value) {
                                return array('groups' => $value);
                            })
                        ->end()
                        ->children()
                            ->scalarNode('type')
                                ->info('Service type')
                            ->end()
                            ->scalarNode('class')
                                ->info('Service class')
                                ->defaultValue('Bridge\Service')
                            ->end()
                            ->variableNode('options')
                                ->info('Custom options')
                                ->defaultValue(array())
                            ->end()
                            ->arrayNode('groups')
                                ->info('List of groups')
                                ->useAttributeAsKey('name')
                                ->prototype('array')
                                    ->info('Group definition')
                                    ->beforeNormalization()
                                        ->ifTrue(function ($value) {
                                            return !array_key_exists('actions', $value);
                                        })
                                        ->then(function ($value) {
                                            return array('actions' => $value);
                                        })
                                    ->end()
                                    ->children()
                                        ->scalarNode('type')
                                            ->info('Group type')
                                        ->end()
                                        ->scalarNode('class')
                                            ->info('Group class')
                                            ->defaultValue('Bridge\Group')
                                        ->end()
                                        ->variableNode('options')
                                            ->info('Custom options')
                                            ->defaultValue(array())
                                        ->end()
                                        ->arrayNode('actions')
                                            ->info('List of actions')
                                            ->useAttributeAsKey('name')
                                            ->prototype('array')
                                                ->info('Action definition')
                                                ->beforeNormalization()
                                                    ->ifTrue(function ($value) {
                                                        return !array_key_exists('options', $value);
                                                    })
                                                    ->then(function ($value) {
                                                        return array('options' => $value);
                                                    })
                                                ->end()
                                                ->children()
                                                    ->scalarNode('type')
                                                        ->info('Action type')
                                                    ->end()
                                                    ->scalarNode('class')
                                                        ->info('Action class')
                                                        ->defaultValue('Bridge\Action\NullAction')
                                                    ->end()
                                                    ->variableNode('options')
                                                        ->info('Custom options')
                                                    ->end()
                                                ->end()
                                            ->end()
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
