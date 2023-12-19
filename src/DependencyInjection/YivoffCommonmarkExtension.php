<?php

declare(strict_types=1);

namespace Yivoff\CommonmarkBundle\DependencyInjection;

use League\CommonMark;
use Symfony\Component\DependencyInjection;
use Twig\Extension\AbstractExtension;
use Yivoff\CommonmarkBundle;

use function class_exists;

/**
 * @internal
 */
class YivoffCommonmarkExtension extends DependencyInjection\Extension\Extension
{
    public const SERVICE_TAG = CommonmarkBundle\YivoffCommonmarkBundle::BUNDLE_PREFIX.'_converter';

    public const CLASS_MATRIX
                             = [
                                 'github'     => CommonMark\GithubFlavoredMarkdownConverter::class,
                                 'commonmark' => CommonMark\CommonMarkConverter::class,
                                 'custom'     => CommonMark\MarkdownConverter::class,
                             ];

    public function load(array $configs, DependencyInjection\ContainerBuilder $container): void
    {
        $configuration = $this->processConfiguration(new Configuration(), $configs);

        $this->registerTwigService($container);

        foreach ($configuration['converters'] as $name => $converterConfiguration) {
            $this->registerConverterService($name, $converterConfiguration, $container);
        }

        $container->setParameter(CommonmarkBundle\YivoffCommonmarkBundle::BUNDLE_PREFIX.'.converters', $configuration['converters']);
    }

    /**
     * Create service definitions for the bundle.
     */
    private function registerTwigService(DependencyInjection\ContainerBuilder $container): void
    {
        if (!class_exists(AbstractExtension::class)) {
            return;
        }

        $container
            ->register(CommonmarkBundle\YivoffCommonmarkBundle::BUNDLE_PREFIX.'.twig_extension', CommonmarkBundle\Twig\CommonMarkExtension::class)
            ->addTag('twig.extension')
            ->setPublic(false)
        ;
    }

    private function registerConverterService(string $converterName, array $converterConfiguration, DependencyInjection\ContainerBuilder $container): void
    {
        // Create converter definition
        $converterDefinition = new DependencyInjection\Definition(self::CLASS_MATRIX[$converterConfiguration['type']]);
        $converterDefinition->setPublic(true);

        // for non-custom converters, "options" go the converter
        if ('custom' !== $converterConfiguration['type']) {
            $converterDefinition->addArgument($converterConfiguration['options'] ?? []);
        } else {
            // for custom converters, go into the environment
            $environmentDefinition = new DependencyInjection\Definition(CommonMark\Environment\Environment::class);
            $environmentDefinition->addArgument($converterConfiguration['options'] ?? []);

            // custom  converters use extensions
            foreach ($converterConfiguration['extensions'] as $extensionName) {
                if (false === $container->has($extensionName)) {
                    $definition = new DependencyInjection\Definition($extensionName);
                    $container->setDefinition($extensionName, $definition);
                }

                $environmentDefinition
                    ->addMethodCall('addExtension', [new DependencyInjection\Reference($extensionName)])
                ;
            }

            $environmentServiceId = CommonmarkBundle\YivoffCommonmarkBundle::BUNDLE_PREFIX.'.environment.'.$converterName;
            $container->setDefinition($environmentServiceId, $environmentDefinition);

            $converterDefinition->addArgument(new DependencyInjection\Reference($environmentServiceId));
        }

        // Current service ID
        $converterServiceId = CommonmarkBundle\YivoffCommonmarkBundle::BUNDLE_PREFIX.'.converters.'.$converterName;
        $container->setDefinition($converterServiceId, $converterDefinition)
            ->addTag(self::SERVICE_TAG)
        ;

        if (interface_exists(CommonMark\MarkdownConverterInterface::class)) {
            $container->registerAliasForArgument($converterServiceId, CommonMark\MarkdownConverterInterface::class, $converterName)->setPublic(true);
        }
        $container->registerAliasForArgument($converterServiceId, CommonMark\ConverterInterface::class, $converterName)->setPublic(true);
    }

    // XML made me unhappy
    // public function getNamespace(): string
    // {
    //     return 'https://yivoff.com/schema/dic/commonmark-bundle';
    // }
    //
    // public function getXsdValidationBasePath(): string
    // {
    //     return __DIR__ . '/../Resources/config/schema';
    // }
}
