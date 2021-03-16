<?php

declare(strict_types=1);

namespace Majermi4\FriendlyConfig;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;

class RegisterConfigService
{
    /**
     * @param class-string        $configClass
     * @param array<string,mixed> $processedConfig
     */
    public static function fromProcessedConfig(string $configClass, array $processedConfig, ContainerBuilder $containerBuilder): Definition
    {
        $definition = new Definition($configClass, [$configClass, $processedConfig]);
        $definition->setFactory([InitializeConfigObject::class, 'fromProcessedConfig']);

        $containerBuilder->setDefinition($configClass, $definition);

        return $definition;
    }
}
