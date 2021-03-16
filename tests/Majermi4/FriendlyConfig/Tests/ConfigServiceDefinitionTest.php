<?php

declare(strict_types=1);

namespace Majermi4\FriendlyConfig\Tests;

use Majermi4\FriendlyConfig\FriendlyConfiguration;
use Majermi4\FriendlyConfig\InitializeConfigObject;
use Majermi4\FriendlyConfig\InitializeConfigServiceDefinition;
use Majermi4\FriendlyConfig\Tests\Fixtures\FixtureConfig;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Config\Definition\Processor;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class ConfigServiceDefinitionTest extends TestCase
{
    public function testRegisteringConfigServiceDefinition(): void
    {
        $configValues = [
            'integerProperty' => 5,
            'scalarTypesConfig' => [
                'integerProperty' => 1,
                'stringProperty' => 'some string value',
                'floatProperty' => 5.3,
                'boolProperty' => false,
            ],
        ];

        $configuration = FriendlyConfiguration::fromClass(FixtureConfig::class, 'TestConfig');
        $processedConfig = (new Processor())->processConfiguration(
            $configuration,
            [$configValues]
        );

        // Register config object as a service
        $containerBuilder = new ContainerBuilder();
        $configServiceDefinition = InitializeConfigServiceDefinition::fromProcessedConfig(FixtureConfig::class, $processedConfig);
        $configServiceDefinition->setPublic(true);
        $containerBuilder->setDefinition(FixtureConfig::class, $configServiceDefinition);

        $containerBuilder->compile();
        static::assertTrue($containerBuilder->isCompiled());

        $fixtureConfigService = $containerBuilder->get(FixtureConfig::class);
        $expectedConfigObject = InitializeConfigObject::fromProcessedConfig(FixtureConfig::class, $processedConfig);
        static::assertTrue($expectedConfigObject->equals($fixtureConfigService), 'Registered service is not equal to the expected object values.');
    }
}
