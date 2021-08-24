<?php

declare(strict_types=1);

namespace Yivoff\CommonmarkBundle;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;
use Yivoff\CommonmarkBundle\DependencyInjection\Compiler\AddConvertersToTwigExtensionPass;

class YivoffCommonmarkBundle extends Bundle
{
    public const BUNDLE_PREFIX = 'yivoff_commonmark';

    /**
     * @codeCoverageIgnore
     */
    public function build(ContainerBuilder $container): void
    {
        parent::build($container);
        $container->addCompilerPass(new AddConvertersToTwigExtensionPass());
    }
}
