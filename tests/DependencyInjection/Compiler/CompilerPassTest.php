<?php

declare(strict_types=1);

namespace Tests\Yivoff\CommonmarkBundle\DependencyInjection\Compiler;

use PHPUnit\Framework\TestCase;
use Symfony\Component\DependencyInjection;
use Symfony\Component\DependencyInjection\ServiceLocator;
use Yivoff\CommonmarkBundle\DependencyInjection\Compiler\AddConvertersToTwigExtensionPass;
use Yivoff\CommonmarkBundle\DependencyInjection\YivoffCommonmarkExtension;
use Yivoff\CommonmarkBundle\YivoffCommonmarkBundle;

class CompilerPassTest extends TestCase
{
    public function testDefinition(): void
    {
        $container    = new DependencyInjection\ContainerBuilder();
        $compilerPass = new AddConvertersToTwigExtensionPass();

        $container->register(YivoffCommonmarkBundle::BUNDLE_PREFIX.'.twig_extension')
            ->setPublic(true)
            ->setClass(MockTwigExtension::class)
        ;

        $container->register('foo')
            ->setClass(Foo::class)
            ->addTag(YivoffCommonmarkExtension::SERVICE_TAG)
        ;

        $container->register('bar')
            ->setClass(Bar::class)
            ->addTag(YivoffCommonmarkExtension::SERVICE_TAG)
        ;

        $compilerPass->process($container);

        $container->compile();

        /** @var \Tests\Yivoff\CommonmarkBundle\DependencyInjection\Compiler\MockTwigExtension $extension */
        $extension = $container->get(YivoffCommonmarkBundle::BUNDLE_PREFIX.'.twig_extension');

        $this->assertInstanceOf(ServiceLocator::class, $extension->serviceLocator);
        $this->assertCount(2, $extension->serviceLocator->getProvidedServices());

        $this->assertTrue($extension->serviceLocator->has('foo'));
        $this->assertInstanceOf(Foo::class, $extension->serviceLocator->get('foo'));

        $this->assertTrue($extension->serviceLocator->has('bar'));
        $this->assertInstanceOf(Bar::class, $extension->serviceLocator->get('bar'));
    }
}

class MockTwigExtension
{
    public function __construct(public ServiceLocator $serviceLocator)
    {
    }
}

class Foo
{
}

class Bar
{
}
