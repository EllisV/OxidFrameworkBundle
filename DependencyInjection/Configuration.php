<?php

/*
 * This file is part of the OXID package.
 *
 * (c) Eligijus Vitkauskas <eligijusvitkauskas@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Ellis\Oxid\Bundle\FrameworkBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * This is the class that validates and merges configuration from your app/config files
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/extension.html#cookbook-bundles-extension-config-class}
 */
class Configuration implements ConfigurationInterface
{
    /**
     * {@inheritdoc}
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('framework');

        $rootNode
            ->children()
                ->arrayNode('database')
                    ->isRequired()
                    ->children()
                        ->scalarNode('host')
                            ->defaultValue('127.0.0.1')
                            ->validate()
                                ->ifTrue(function ($v) {
                                    return !filter_var($v, FILTER_VALIDATE_IP);
                                })
                                ->thenInvalid('Host must be a valid IP address.')
                            ->end()
                        ->end()
                        ->scalarNode('name')->isRequired()->end()
                        ->scalarNode('user')->isRequired()->end()
                        ->scalarNode('password')->defaultNull()->end()
                    ->end()
                ->end()
                ->scalarNode('shop_url')->isRequired()->end()
                ->scalarNode('shop_ssl_url')->defaultNull()->end()
                ->scalarNode('admin_ssl_url')->defaultNull()->end()
                ->scalarNode('compile_dir')->defaultValue('%kernel.cache_dir%/oxid')->end()
            ->end()
        ;

        return $treeBuilder;
    }
}
