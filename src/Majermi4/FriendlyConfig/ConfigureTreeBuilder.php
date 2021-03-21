<?php

declare(strict_types=1);

namespace Majermi4\FriendlyConfig;

use Majermi4\FriendlyConfig\Exception\InvalidConfigClassException;
use Majermi4\FriendlyConfig\Util\StringUtil;
use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
use Symfony\Component\Config\Definition\Builder\NodeBuilder;
use Symfony\Component\Config\Definition\Builder\NodeDefinition;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;

class ConfigureTreeBuilder
{
    /**
     * @param class-string $configClass
     */
    public function __invoke(TreeBuilder $treeBuilder, string $configClass): void
    {
        $rootNode = $treeBuilder->getRootNode();

        if (!$rootNode instanceof ArrayNodeDefinition) {
            throw new \RuntimeException('Root node must be an instance of ArrayNodeDefinition.');
        }

        $this->configureClassNode($configClass, $rootNode);
    }

    /**
     * @param class-string $configClass
     */
    private function configureClassNode(string $configClass, ArrayNodeDefinition $nodeDefinition): void
    {
        $classReflection = new \ReflectionClass($configClass);
        $constructor = $classReflection->getConstructor();

        if ($constructor === null) {
            throw InvalidConfigClassException::missingConstructor($configClass);
        }

        $parameters = $constructor->getParameters();

        if (0 === \count($parameters)) {
            throw InvalidConfigClassException::missingConstructorParameters($configClass);
        }

        foreach ($parameters as $parameter) {
            $parameterType = $parameter->getType();

            if ($parameterType === null) {
                throw InvalidConfigClassException::missingConstructorParameterType($configClass, $parameter->name);
            }

            /* @phpstan-ignore-next-line */
            $parameterTypeName = $parameterType->getName();
            $configParameterName = $configParameterName = StringUtil::toSnakeCase($parameter->name);
            $childrenNodeBuilder = $nodeDefinition->children();
            switch ($parameterTypeName) {
                case ParameterTypes::INT:
                    $this->configureSharedOptions($parameter, $childrenNodeBuilder->integerNode($configParameterName));
                    break;
                case ParameterTypes::STRING:
                    $this->configureSharedOptions($parameter, $childrenNodeBuilder->scalarNode($configParameterName));
                    break;
                case ParameterTypes::BOOL:
                    $this->configureSharedOptions($parameter, $childrenNodeBuilder->booleanNode($configParameterName));
                    break;
                case ParameterTypes::FLOAT:
                    $this->configureSharedOptions($parameter, $childrenNodeBuilder->floatNode($configParameterName));
                    break;
                case ParameterTypes::ARRAY:
                    $this->configureArray($parameter, $childrenNodeBuilder);
                    break;
                default:
                    if (!\class_exists($parameterTypeName)) {
                        throw InvalidConfigClassException::unsupportedConstructorParameterType($parameter);
                    }
                    $this->configureNestedClass($parameter, $childrenNodeBuilder);
                    break;
            }
        }
    }

    private function configureArray(\ReflectionParameter $parameter, NodeBuilder $nodeBuilder): void
    {
        $arrayItemType = PhpDocTypeParser::getParameterArrayItemType($parameter);
        $arrayNode = $nodeBuilder->arrayNode(StringUtil::toSnakeCase($parameter->name));

        switch ($arrayItemType) {
            case ParameterTypes::INT:
                $arrayNode->integerPrototype();
                break;
            case ParameterTypes::STRING:
                $arrayNode->scalarPrototype();
                break;
            case ParameterTypes::BOOL:
                $arrayNode->booleanPrototype();
                break;
            case ParameterTypes::FLOAT:
                $arrayNode->floatPrototype();
                break;
            case ParameterTypes::ARRAY:
                throw InvalidConfigClassException::unsupportedNestedArrayType($parameter, $arrayItemType);
            default:
                if (!class_exists($arrayItemType)) {
                    throw InvalidConfigClassException::unsupportedNestedArrayType($parameter, $arrayItemType);
                }

                $arrayNode->useAttributeAsKey('name'); // In order to preserve keys.
                $this->configureClassNode($arrayItemType, $arrayNode->arrayPrototype());
        }

        $this->configureSharedOptions($parameter, $arrayNode);
    }

    private function configureNestedClass(\ReflectionParameter $parameter, NodeBuilder $nodeBuilder): void
    {
        $arrayNode = $nodeBuilder->arrayNode(StringUtil::toSnakeCase($parameter->name));

        // Do not add default value because it is not allowed by Symfony Config Component to add
        // default values for arrays that represent nested objects (so called concrete nodes).
        $this->configureSharedOptions($parameter, $arrayNode, false);

        /* @phpstan-ignore-next-line */
        $this->configureClassNode($parameter->getType()->getName(), $arrayNode);
    }

    private function configureSharedOptions(\ReflectionParameter $parameter, NodeDefinition $nodeDefinition, bool $addDefaultValueIfApplicable = true): void
    {
        $parameterIsOptional = $parameter->isOptional();
        $parameterHasDefaultValue = $parameter->isDefaultValueAvailable();
        $parameterAllowsNull = $parameter->allowsNull();

        if (!$parameterAllowsNull && !$parameterIsOptional) {
            $nodeDefinition->isRequired();
        }

        if ($parameterHasDefaultValue && $addDefaultValueIfApplicable) {
            $nodeDefinition->defaultValue($parameter->getDefaultValue());
        }
    }
}
