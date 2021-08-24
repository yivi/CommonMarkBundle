<?php

declare(strict_types=1);

namespace Yivoff\CommonmarkBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * @internal
 */
class Configuration implements ConfigurationInterface
{
    public function getConfigTreeBuilder(): TreeBuilder
    {
        $treeBuilder = new TreeBuilder('yivoff_commonmark');

        $treeBuilder->getRootNode()
            ->children()
                ->arrayNode('converters')
                ->useAttributeAsKey('name')
                ->requiresAtLeastOneElement()
                ->isRequired()
                    ->arrayPrototype()
                        ->children()
                            ->enumNode('type')
                                ->values(['commonmark', 'github', 'custom'])
                                ->defaultValue('commonmark')
                                ->example('github')
                            ->end()
                            ->variableNode('options')->end()
                            ->arrayNode('extensions')
                                ->scalarPrototype()
                                ->info('Fully qualified class name for the extensions. Only used for "custom" converter type')
                                ->example('League\CommonMark\CommonMark\Extension\InlinesOnly\InlinesOnlyExtension')
                                ->end()
                        ->end()
                    ->end()
                ->end()
            ->end()
        ;

        return $treeBuilder;
    }
}
