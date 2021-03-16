<?php

declare(strict_types=1);

namespace Majermi4\FriendlyConfig;

use Symfony\Component\DependencyInjection\Definition;

class InitializeConfigServiceDefinition
{
    /**
     * @param class-string        $configClass
     * @param array<string,mixed> $processedConfig
     */
    public static function fromProcessedConfig(string $configClass, array $processedConfig): Definition
    {
        $definition = new Definition($configClass, [$configClass, $processedConfig]);
        $definition->setFactory([InitializeConfigObject::class, 'fromProcessedConfig']);

        return $definition;
    }
}
