<?php

declare(strict_types=1);

namespace Yivoff\CommonmarkBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\Compiler\ServiceLocatorTagPass;
use Yivoff\CommonmarkBundle\DependencyInjection\YivoffCommonmarkExtension;
use Yivoff\CommonmarkBundle\YivoffCommonmarkBundle;
use function array_keys;


/**
 * @internal
 */
class AddConvertersToTwigExtensionPass implements CompilerPassInterface
{
    public function process(DependencyInjection\ContainerBuilder $container): void
    {
        $twigExtensionDefinition = $container->getDefinition(YivoffCommonmarkBundle::BUNDLE_PREFIX.'.twig_extension');
        $serviceIds              = $container->findTaggedServiceIds(YivoffCommonmarkExtension::SERVICE_TAG);

        $services = [];
        foreach (array_keys($serviceIds) as $serviceId) {
            $services[$serviceId] = new DependencyInjection\Reference((string)$serviceId);
        }

        $twigExtensionDefinition->setArgument(0, ServiceLocatorTagPass::register($container, $services));
    }
}
