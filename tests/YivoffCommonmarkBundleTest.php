<?php

declare(strict_types=1);

namespace Tests\Yivoff\CommonmarkBundle;

use PHPUnit\Framework\TestCase;
use Yivoff\CommonmarkBundle\DependencyInjection\YivoffCommonmarkExtension;
use Yivoff\CommonmarkBundle\YivoffCommonmarkBundle;

/**
 * @internal
 * @coversNothing
 */
class YivoffCommonmarkBundleTest extends TestCase
{
    public function testExtensionNameConsistency(): void
    {
        $bundle    = new YivoffCommonmarkBundle();
        $extension = $bundle->getContainerExtension();

        self::assertInstanceOf(YivoffCommonmarkExtension::class, $extension);
    }
}
