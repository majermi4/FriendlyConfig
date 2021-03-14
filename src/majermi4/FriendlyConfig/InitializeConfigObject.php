<?php

declare(strict_types=1);

namespace majermi4\FriendlyConfig;

class InitializeConfigObject
{
    /**
     * @param class-string        $configClass
     * @param array<string,mixed> $parsedConfig
     */
    public static function fromParsedConfig(string $configClass, array $parsedConfig): object
    {
        $configClassReflection = new \ReflectionClass($configClass);
        $constructor = $configClassReflection->getConstructor();
        $parameters = $constructor->getParameters();

        $resolvedParameters = [];

        foreach ($parameters as $parameter) {
            if ($parameter->isOptional() && !\array_key_exists($parameter->getName(), $parsedConfig)) {
                break;
            }

            if (!isset($parsedConfig[$parameter->getName()])) {
                $resolvedParameter = null;
            } else if (class_exists($parameter->getType()->getName())) {
                $resolvedParameter = self::fromParsedConfig($parameter->getType()->getName(), $parsedConfig[$parameter->getName()]);
            } else {
                $resolvedParameter = $parsedConfig[$parameter->getName()];
            }

            $resolvedParameters[$parameter->getName()] = $resolvedParameter;
        }

        return $configClassReflection->newInstanceArgs($resolvedParameters);
    }
}
