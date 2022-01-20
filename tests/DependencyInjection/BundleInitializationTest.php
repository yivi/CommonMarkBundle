<?php

declare(strict_types=1);

namespace Tests\Yivoff\CommonmarkBundle\DependencyInjection;

use League\CommonMark;
use PHPUnit\Framework\TestCase;
use Symfony\Component\DependencyInjection;
use Yivoff\CommonmarkBundle\DependencyInjection\YivoffCommonmarkExtension;
use Yivoff\CommonmarkBundle\YivoffCommonmarkBundle;
use function lcfirst;
use function sprintf;
use function str_replace;
use function ucwords;

/**
 * @internal
 * @coversNothing
 */
class BundleInitializationTest extends TestCase
{
    public function testBasicCommonmarkConfiguration(): void
    {
        // Get the container
        $container = new DependencyInjection\ContainerBuilder();
        $extension = new YivoffCommonmarkExtension();

        $config = [
            'converters' => [
                'default' => [],
            ],
        ];

        $extension->load([$config], $container);

        $container->compile();

        // Test if your services exists
        $this->assertTrue($container->has(YivoffCommonmarkBundle::BUNDLE_PREFIX.'.converters.default'));

        $service = $container->get(YivoffCommonmarkBundle::BUNDLE_PREFIX.'.converters.default');
        $this->assertInstanceOf(CommonMark\CommonMarkConverter::class, $service);
    }

    public function testMultipleConverters(): void
    {
        $container = new DependencyInjection\ContainerBuilder();
        $extension = new YivoffCommonmarkExtension();

        $config = [
            'converters' => [
                'commonmark_converter' => [
                    'type' => 'commonmark',
                ],
                'github_converter'     => [
                    'type' => 'github',
                ],
                'empty_converter'      => [
                    'type' => 'custom',
                ],
            ],
        ];

        $converters = [
            'commonmark_converter' => CommonMark\CommonMarkConverter::class,
            'github_converter'     => CommonMark\GithubFlavoredMarkdownConverter::class,
            'empty_converter'      => CommonMark\MarkdownConverter::class,
        ];

        $extension->load([$config], $container);

        foreach ($converters as $converterName => $converterClass) {
            $serviceId = YivoffCommonmarkBundle::BUNDLE_PREFIX.".converters.{$converterName}";

            $this->assertTrue($container->has($serviceId));
            $service = $container->get($serviceId);
            $this->assertInstanceOf($converterClass, $service);

            $param = lcfirst(str_replace(' ', '', ucwords(str_replace('_', ' ', $converterName))));
            $alias = sprintf(CommonMark\MarkdownConverterInterface::class.' $%s', $param);

            self::assertTrue($container->hasAlias($alias));
        }
    }

    public function testEmptyConverterRegistersExtensions(): void
    {
        $container = new DependencyInjection\ContainerBuilder();
        $extension = new YivoffCommonmarkExtension();

        $convertersConfig = [
            'converters' => [
                'empty_converter' => [
                    'type'       => 'custom',
                    'extensions' => [
                        CommonMark\Extension\Autolink\AutolinkExtension::class,
                        CommonMark\Extension\InlinesOnly\InlinesOnlyExtension::class,
                    ],
                ],
            ],
        ];

        $extension->load([$convertersConfig], $container);
        $container->compile();

        $serviceId = YivoffCommonmarkBundle::BUNDLE_PREFIX.'.converters.empty_converter';
        $this->assertTrue($container->has($serviceId));

        $expectedExtensions = [
            CommonMark\Extension\Autolink\AutolinkExtension::class       => true,
            CommonMark\Extension\InlinesOnly\InlinesOnlyExtension::class => true,
        ];

        $service = $container->get($serviceId);

        $this->assertInstanceOf(CommonMark\MarkdownConverter::class, $service);
        $actualExtensions = $service->getEnvironment()->getExtensions();

        foreach ($actualExtensions as $extension) {
            $this->assertArrayHasKey($extension::class, $expectedExtensions);
            unset($expectedExtensions[$extension::class]);
        }

        $this->assertEmpty($expectedExtensions);
    }
}
