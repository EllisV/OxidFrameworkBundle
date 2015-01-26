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

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\Config\FileLocator;

/**
 * This is the class that loads and manages your bundle configuration
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/extension.html}
 */
class FrameworkExtension extends Extension
{
    /**
     * {@inheritdoc}
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        $this->addContainerParametersFromConfig($config, $container);

        $loader = new YamlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('services.yml');
    }

    /**
     * Restersize config parameters and add them to container
     *
     * @param array            $config
     * @param ContainerBuilder $container
     * @param string           $prepend
     *
     * @return array
     */
    private function addContainerParametersFromConfig(array $config, ContainerBuilder $container, $prepend = 'oxid.')
    {
        foreach ($config as $key => $value) {
            if (is_array($value)) {
                $this->addContainerParametersFromConfig($value, $container, $prepend.$key.'.');
                continue;
            }

            $container->setParameter($prepend.$key, $value);
        }
    }
}
