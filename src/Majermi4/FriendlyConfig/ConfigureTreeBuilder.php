<?php

declare(strict_types=1);

namespace Majermi4\FriendlyConfig;

use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
use Symfony\Component\Config\Definition\Builder\NodeBuilder;
use Symfony\Component\Config\Definition\Builder\NodeDefinition;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;

class ConfigureTreeBuilder
{
    /**
     * @var class-string
     */
    private string $configClass;

    /**
     * @param class-string $configClass
     */
    public function __construct(string $configClass)
    {
        $this->configClass = $configClass;
    }

    public function __invoke(TreeBuilder $treeBuilder): void
    {
        $rootNode = $treeBuilder->getRootNode();

        $this->configureClassNode($this->configClass, $rootNode);
    }

    private function configureClassNode(string $configClass, ArrayNodeDefinition $nodeDefinition): void
    {
        $classReflection = new \ReflectionClass($configClass);
        $constructor = $classReflection->getConstructor();

        if (null === $constructor) {
            throw new \LogicException('1');
        }

        $parameters = $constructor->getParameters();

        if (0 === \count($parameters)) {
            throw new \LogicException('2');
        }

        foreach ($parameters as $parameter) {
            $parameterType = $parameter->getType()->getName();
            $childrenNodeBuilder = $nodeDefinition->children();
            switch ($parameterType) {
                case ParameterTypes::INT:
                    $this->configureSharedOptions($parameter, $childrenNodeBuilder->integerNode($parameter->name));
                    break;
                case ParameterTypes::STRING:
                    $this->configureSharedOptions($parameter, $childrenNodeBuilder->scalarNode($parameter->name));
                    break;
                case ParameterTypes::BOOL:
                    $this->configureSharedOptions($parameter, $childrenNodeBuilder->booleanNode($parameter->name));
                    break;
                case ParameterTypes::FLOAT:
                    $this->configureSharedOptions($parameter, $childrenNodeBuilder->floatNode($parameter->name));
                    break;
                case ParameterTypes::ARRAY:
                    $this->configureArray($parameter, $childrenNodeBuilder);
                    break;
                default:
                    if (!\class_exists($parameterType)) {
                        throw new \LogicException('3');
                    }
                    $this->configureNestedClass($parameter, $childrenNodeBuilder);
                    break;
            }
        }
    }

    private function configureArray(\ReflectionParameter $parameter, NodeBuilder $nodeBuilder): void
    {
        $arrayItemType = PhpDocTypeParser::getParameterArrayItemType($parameter);
        $arrayNode = $nodeBuilder->arrayNode($parameter->name);

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
            //case 'array': $arrayNode->arrayPrototype(); break;
            default:
                return; // TODO
//                $this->configureNestedClass();
                break;
        }

        $this->configureSharedOptions($parameter, $arrayNode);
    }

    private function configureNestedClass(\ReflectionParameter $parameter, NodeBuilder $nodeBuilder): void
    {
        $arrayNode = $nodeBuilder->arrayNode($parameter->name);

        // Do not add default value because it is not allowed by Symfony Config Component to add
        // default values for arrays that represent nested objects (so called concrete nodes).
        $this->configureSharedOptions($parameter, $arrayNode, false);

        $this->configureClassNode($parameter->getType()->getName(), $arrayNode);
    }

    private function configureSharedOptions(\ReflectionParameter $parameter, NodeDefinition $nodeDefinition, bool $addDefaultValueIfApplicable = true): void
    {
        $parameterIsOptional = $parameter->isOptional();
        $parameterHasDefaultValue = $parameter->isDefaultValueAvailable();
        $parameterAllowsNull = $parameter->allowsNull();
        $parameterType = $parameter->getType()->getName();

        if (!$parameterAllowsNull && !$parameterIsOptional) {
            $nodeDefinition->isRequired();
        }

        if ($parameterHasDefaultValue && $addDefaultValueIfApplicable) {
            $nodeDefinition->defaultValue($parameter->getDefaultValue());
        }

//        if (!$parameter->isOptional()) {
//            $nodeDefinition->isRequired();
//        } else if (null !== $parameter->getDefaultValue()) {
//            $nodeDefinition->defaultValue($parameter->getDefaultValue());
//        } else if ($parameter->allowsNull()) {
//            $nodeDefinition->defaultNull();
//        }
    }
}
