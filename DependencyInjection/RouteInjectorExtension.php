<?php

namespace SecIT\RouteInjectorBundle\DependencyInjection;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;

/**
 * Class RouteInjectorExtension.
 *
 * @author Tomasz Gemza
 */
class RouteInjectorExtension extends Extension
{
    /**
     * {@inheritdoc}
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $loader = new Loader\YamlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('services.yml');

        $cacheDirectory = '%kernel.cache_dir%/secit/route_injector';
        $cacheDirectory = $container->getParameterBag()->resolveValue($cacheDirectory);
        if (!is_dir($cacheDirectory)) {
            mkdir($cacheDirectory, 0777, true);
        }

        $container
            ->getDefinition('secit.route_injector.metadata.cache')
            ->setArguments([$cacheDirectory])
        ;
    }
}
