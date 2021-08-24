<?php

declare(strict_types=1);

namespace Tests\Yivoff\CommonmarkBundle\Functional;

use Nyholm\BundleTest\TestKernel;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Bundle\TwigBundle\TwigBundle;
use Yivoff\CommonmarkBundle\YivoffCommonmarkBundle;

/**
 * @internal
 * @coversNothing
 */
class TwigIntegrationTest extends KernelTestCase
{
    public function testBasicConverter(): void
    {
        self::bootKernel([
            'config' => static function (TestKernel $kernel): void {
                $kernel->addTestConfig(__DIR__.'/../Fixtures/config/configuration_basic.yaml');
            },
        ]);

        $container = self::getContainer();

        /** @var \Twig\Environment $twig */
        $twig         = $container->get('twig');
        $template     = $twig->createTemplate('{{ md|commonmark }}');
        $actualHtml   = $twig->render($template, ['md' => '# Hello _World!_']);
        $expectedHtml = '<h1>Hello <em>World!</em></h1>';

        $this->assertEquals($expectedHtml, trim($actualHtml));
    }

    public function testGithubConverter(): void
    {
        self::bootKernel([
            'config' => static function (TestKernel $kernel): void {
                $kernel->addTestConfig(__DIR__.'/../Fixtures/config/configuration_multiple_converters.yaml');
            },
        ]);
        $container = self::getContainer();

        /** @var \Twig\Environment $twig */
        $twig       = $container->get('twig');
        $template   = $twig->createTemplate("{{ md|commonmark('gh_converter') }}");
        $actualHtml = $twig->render($template, ['md' => '# Hello _World!_']);

        $expectedHtml = '<h1>Hello <em>World!</em></h1>';

        $this->assertEquals($expectedHtml, trim($actualHtml));
    }

    protected static function getKernelClass(): string
    {
        return TestKernel::class;
    }

    protected static function createKernel(array $options = []): TestKernel
    {
        /**
         * @var TestKernel $kernel
         */
        $kernel = parent::createKernel($options);
        $kernel->addTestBundle(YivoffCommonmarkBundle::class);
        $kernel->addTestBundle(TwigBundle::class);

        $kernel->handleOptions($options);

        return $kernel;
    }
}
