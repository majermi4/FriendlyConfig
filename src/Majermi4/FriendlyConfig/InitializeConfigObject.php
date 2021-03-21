<?php

declare(strict_types=1);

namespace Majermi4\FriendlyConfig;

use Majermi4\FriendlyConfig\Exception\InvalidConfigClassException;
use Majermi4\FriendlyConfig\PhpDocParser\ArrayItemTypeParser;
use Majermi4\FriendlyConfig\Util\StringUtil;

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

        if ($constructor === null) {
            throw InvalidConfigClassException::missingConstructor($configClass);
        }

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
            } elseif ($parameterType === ParameterTypes::ARRAY) {
                $arrayItemType = ArrayItemTypeParser::getParameterArrayItemType($parameter);
                $resolvedParameter = [];
                foreach ($processedConfig[$parameterName] as $idx => $item) {
                    $resolvedParameterItem = $item;
                    if (class_exists($arrayItemType->getValueType())) {
                        $resolvedParameterItem = InitializeConfigObject::fromProcessedConfig($arrayItemType->getValueType(), $item);
                    }
                    if ($arrayItemType->getKeyType() === 'string') {
                        $resolvedParameter[$idx] = $resolvedParameterItem;
                    } else {
                        $resolvedParameter[] = $resolvedParameterItem;
                    }
                }
            } else {
                $resolvedParameter = $processedConfig[$parameterName];
            }

            $resolvedParameters[] = $resolvedParameter;
        }

        return $configClassReflection->newInstanceArgs($resolvedParameters);
    }
}
