<?php

declare(strict_types=1);

namespace Tests\Yivoff\CommonmarkBundle\Functional;

use League\CommonMark\CommonMarkConverter;
use League\CommonMark\ConverterInterface;
use League\CommonMark\GithubFlavoredMarkdownConverter;
use Nyholm\BundleTest\TestKernel;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Yivoff\CommonmarkBundle\YivoffCommonmarkBundle;

use function trim;

/**
 * @internal
 *
 * @coversNothing
 */
class ServiceIntegrationTest extends KernelTestCase
{
    public function testBasicConverter(): void
    {
        self::bootKernel([
            'config' => static function (TestKernel $kernel): void {
                $kernel->addTestConfig(__DIR__.'/../Fixtures/config/configuration_basic.yaml');
                $kernel->addTestConfig(__DIR__.'/../Fixtures/config/framework-config.yaml');
            },
        ]);

        $container = self::getContainer();
        $converter = $container->get(YivoffCommonmarkBundle::BUNDLE_PREFIX.'.converters.default');

        $this->assertInstanceOf(CommonMarkConverter::class, $converter);

        $markdown     = '# Hello _World!_';
        $actualHtml   = $converter->convert($markdown)->getContent();
        $expectedHtml = '<h1>Hello <em>World!</em></h1>';

        $this->assertEquals($expectedHtml, trim($actualHtml));
    }

    public function testGithubConverter(): void
    {
        self::bootKernel([
            'config' => static function (TestKernel $kernel): void {
                $kernel->addTestConfig(__DIR__.'/../Fixtures/config/configuration_multiple_converters.yaml');
                $kernel->addTestConfig(__DIR__.'/../Fixtures/config/framework-config.yaml');
            },
        ]);
        $container = self::getContainer();

        $aliasedConverter = $container->get(ConverterInterface::class.' $ghConverter');
        $converter        = $container->get(YivoffCommonmarkBundle::BUNDLE_PREFIX.'.converters.gh_converter');

        $this->assertInstanceOf(GithubFlavoredMarkdownConverter::class, $converter);
        $this->assertInstanceOf(GithubFlavoredMarkdownConverter::class, $aliasedConverter);

        $markdown     = '# Hello _World!_';
        $expectedHtml = '<h1>Hello <em>World!</em></h1>';

        $actualHtml = $converter->convert($markdown)->getContent();

        $this->assertEquals($expectedHtml, trim($actualHtml));
    }

    public function testCommonmarkWithOptions(): void
    {
        self::bootKernel([
            'config' => static function (TestKernel $kernel): void {
                $kernel->addTestConfig(__DIR__.'/../Fixtures/config/configuration_multiple_converters.yaml');
                $kernel->addTestConfig(__DIR__.'/../Fixtures/config/framework-config.yaml');
            },
        ]);
        $container = self::getContainer();

        $converter = $container->get(YivoffCommonmarkBundle::BUNDLE_PREFIX.'.converters.my_converter');

        $this->assertInstanceOf(CommonMarkConverter::class, $converter);

        $markdown     = '# Hello _World!_';
        $actualHtml   = $converter->convert($markdown)->getContent();
        $expectedHtml = '<h1>Hello _World!_</h1>';

        $this->assertEquals($expectedHtml, trim($actualHtml));
    }

    protected static function createKernel(array $options = []): TestKernel
    {
        /**
         * @var TestKernel $kernel
         */
        $kernel = parent::createKernel($options);
        $kernel->addTestBundle(YivoffCommonmarkBundle::class);

        $kernel->handleOptions($options);

        return $kernel;
    }

    protected static function getKernelClass(): string
    {
        return TestKernel::class;
    }
}
