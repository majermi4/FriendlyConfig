<?php

declare(strict_types=1);

namespace Majermi4\FriendlyConfig\Tests;

use Majermi4\FriendlyConfig\FriendlyConfiguration;
use Majermi4\FriendlyConfig\InitializeConfigObject;
use Majermi4\FriendlyConfig\Tests\Util\BaseTestConfig;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Config\Definition\Processor;

abstract class ConfigurationTestCase extends TestCase
{
    /**
     * @dataProvider validConfigurationProvider
     *
     * @param array<string,mixed> $configValues
     */
    public function testType(BaseTestConfig $configObject, array $configValues): void
    {
        $configuration = FriendlyConfiguration::fromClass(get_class($configObject), 'TestConfig');

        $processedConfig = (new Processor())->processConfiguration(
            $configuration,
            [$configValues]
        );

        $processedConfigObject = InitializeConfigObject::fromProcessedConfig(get_class($configObject), $processedConfig);

        static::assertTrue($configObject->equals($processedConfigObject), 'Expected config object value does not match the processed config values.');
    }

    /**
     * @return array<string,array<mixed>>
     */
    public function validConfigurationProvider(): array
    {
        return [];
    }
}
