<?php

declare(strict_types=1);

namespace Tests\Yivoff\CommonmarkBundle\Configuration;

use Matthias\SymfonyConfigTest\PhpUnit\ConfigurationTestCaseTrait;
use PHPUnit\Framework\TestCase;
use Yivoff\CommonmarkBundle\DependencyInjection\Configuration;

/**
 * @internal
 *
 * @coversNothing
 */
class ConfigurationTest extends TestCase
{
    use ConfigurationTestCaseTrait;

    public function testEmptyConfigurationIsInvalid(): void
    {
        $this->assertConfigurationIsInvalid(
            [
                [], // no values at all
            ]
        );
    }

    public function testConverterWithoutTypeIsCommonmark(): void
    {
        $this->assertProcessedConfigurationEquals(
            [
                ['converters' => ['default' => []]], // no values at all
            ],
            [
                'converters' => [
                    'default' => [
                        'type'       => 'commonmark',
                        'extensions' => [],
                    ],
                ],
            ]
        );
    }

    public function testConverterMayHaveOptions(): void
    {
        $this->assertProcessedConfigurationEquals(
            [
                [
                    'converters' => [
                        'default' => [
                            'type'    => 'github',
                            'options' => ['foo' => 'bar'],
                        ],
                    ],
                ], // no values at all
            ],
            [
                'converters' => [
                    'default' => [
                        'type'       => 'github',
                        'options'    => [
                            'foo' => 'bar',
                        ],
                        'extensions' => [],
                    ],
                ],
            ]
        );
    }

    public function getConfiguration(): Configuration
    {
        return new Configuration();
    }
}
