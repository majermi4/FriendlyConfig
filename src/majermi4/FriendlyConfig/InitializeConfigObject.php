<?php

declare(strict_types=1);

namespace Majermi4\FriendlyConfig;

class InitializeConfigObject
{
    /**
     * @template T
     *
     * @param class-string<T>     $configClass
     * @param array<string,mixed> $processedConfig
     *
     * @return T
     */
    public static function fromProcessedConfig(string $configClass, array $processedConfig): object
    {
        $configClassReflection = new \ReflectionClass($configClass);
        $constructor = $configClassReflection->getConstructor();
        $parameters = $constructor->getParameters();

        $resolvedParameters = [];

        foreach ($parameters as $parameter) {
            if ($parameter->isOptional() && !\array_key_exists($parameter->getName(), $processedConfig)) {
                break;
            }

            $parameterName = $parameter->name;
            $parameterType = $parameter->getType()->getName();
            if (!isset($processedConfig[$parameterName])) {
                $resolvedParameter = null;
            } elseif (\class_exists($parameterType)) {
                $resolvedParameter = self::fromProcessedConfig($parameterType, $processedConfig[$parameterName]);
            } elseif (ParameterTypes::ARRAY === $parameterType && \class_exists(PhpDocTypeParser::getParameterArrayItemType($parameter))) {
                $resolvedParameter = [];
                foreach ($processedConfig[$parameterName] as $idx => $item) {
                    $resolvedParameter[$idx] = self::fromProcessedConfig(PhpDocTypeParser::getParameterArrayItemType($parameter), $item);
                }
            } else {
                $resolvedParameter = $processedConfig[$parameterName];
            }

            $resolvedParameters[$parameterName] = $resolvedParameter;
        }

        return $configClassReflection->newInstanceArgs($resolvedParameters);
    }
}
