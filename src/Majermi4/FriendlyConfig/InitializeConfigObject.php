<?php

declare(strict_types=1);

namespace Majermi4\FriendlyConfig;

use Majermi4\FriendlyConfig\Util\StringUtil;
use Webmozart\Assert\Assert;

class InitializeConfigObject
{
    /**
     * @template T of object
     *
     * @param class-string<T>     $configClass
     * @param array<string,mixed> $processedConfig
     *
     * @return T of object
     */
    public static function fromProcessedConfig(string $configClass, array $processedConfig): object
    {
        $configClassReflection = new \ReflectionClass($configClass);
        $constructor = $configClassReflection->getConstructor();
        Assert::notNull($constructor); // TODO: Change to exception
        $parameters = $constructor->getParameters();

        $resolvedParameters = [];

        foreach ($parameters as $parameter) {
            $parameterName = StringUtil::toSnakeCase($parameter->name);

            if ($parameter->isOptional() && !\array_key_exists($parameterName, $processedConfig)) {
                break;
            }

            /* @phpstan-ignore-next-line */
            $parameterType = $parameter->getType()->getName();
            if (!isset($processedConfig[$parameterName])) {
                $resolvedParameter = null;
            } elseif (\class_exists($parameterType)) {
                $resolvedParameter = InitializeConfigObject::fromProcessedConfig($parameterType, $processedConfig[$parameterName]);
            } elseif (ParameterTypes::ARRAY === $parameterType && \class_exists(PhpDocTypeParser::getParameterArrayItemType($parameter))) {
                $resolvedParameter = [];
                foreach ($processedConfig[$parameterName] as $idx => $item) {
                    $resolvedParameter[$idx] = InitializeConfigObject::fromProcessedConfig(PhpDocTypeParser::getParameterArrayItemType($parameter), $item);
                }
            } else {
                $resolvedParameter = $processedConfig[$parameterName];
            }

            $resolvedParameters[] = $resolvedParameter;
        }

        return $configClassReflection->newInstanceArgs($resolvedParameters);
    }
}
