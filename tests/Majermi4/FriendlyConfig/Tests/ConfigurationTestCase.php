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

        $parsedConfig = (new Processor())->processConfiguration(
            $configuration,
            [$configValues]
        );

        $parsedConfigObject = InitializeConfigObject::fromParsedConfig(get_class($configObject), $parsedConfig);

        static::assertTrue($configObject->equals($parsedConfigObject), 'Expected config object value does not match the processed config values.');
    }

    public function validConfigurationProvider(): array
    {
        return [];
    }
}
