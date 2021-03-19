<?php

declare(strict_types=1);

namespace Majermi4\FriendlyConfig\Tests;

use Majermi4\FriendlyConfig\FriendlyConfiguration;
use Majermi4\FriendlyConfig\InitializeConfigObject;
use Majermi4\FriendlyConfig\RegisterConfigService;
use Majermi4\FriendlyConfig\Tests\Fixtures\FixtureConfig;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Config\Definition\Processor;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class ConfigServiceDefinitionTest extends TestCase
{
    public function testRegisteringConfigServiceDefinition(): void
    {
        $configValues = [
            'integer_property' => 5,
            'scalar_types_config' => [
                'integer_property' => 1,
                'string_property' => 'some string value',
                'float_property' => 5.3,
                'bool_property' => false,
            ],
        ];

        $configuration = FriendlyConfiguration::fromClass(FixtureConfig::class, 'TestConfig');
        $processedConfig = (new Processor())->processConfiguration(
            $configuration,
            [$configValues]
        );

        // Register config object as a service
        $containerBuilder = new ContainerBuilder();
        $configServiceDefinition = RegisterConfigService::fromProcessedConfig(FixtureConfig::class, $processedConfig, $containerBuilder);
        $configServiceDefinition->setPublic(true);

        $containerBuilder->compile();
        static::assertTrue($containerBuilder->isCompiled());

        /** @var FixtureConfig $fixtureConfigService */
        $fixtureConfigService = $containerBuilder->get(FixtureConfig::class);
        $expectedConfigObject = InitializeConfigObject::fromProcessedConfig(FixtureConfig::class, $processedConfig);
        static::assertTrue($expectedConfigObject->equals($fixtureConfigService), 'Registered service is not equal to the expected object values.');
    }
}
