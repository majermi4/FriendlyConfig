<?php

declare(strict_types=1);

namespace majermi4\FriendlyConfig;

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
            switch ($parameterType) {
                case 'int':
                    $this->configureInteger($parameter, $nodeDefinition->children());
                    break;
                case 'string':
                    $this->configureString($parameter, $nodeDefinition->children());
                    break;
                case 'bool':
                    $this->configureBoolean($parameter, $nodeDefinition->children());
                    break;
                case 'float':
                    $this->configureFloat($parameter, $nodeDefinition->children());
                    break;
                case 'array':
                    $this->configureArray($parameter, (string) $constructor->getDocComment(), $nodeDefinition->children());
                    break;
                default:
                    $this->configureNestedClass($parameter, $nodeDefinition->children());
                    break;
            }
        }
    }

    private function configureInteger(\ReflectionParameter $parameter, NodeBuilder $nodeBuilder): void
    {
        $this->configureSharedOptions($parameter, $nodeBuilder->integerNode($parameter->name));
    }

    private function configureString(\ReflectionParameter $parameter, NodeBuilder $nodeBuilder): void
    {
        $this->configureSharedOptions($parameter, $nodeBuilder->scalarNode($parameter->name));
    }

    private function configureBoolean(\ReflectionParameter $parameter, NodeBuilder $nodeBuilder): void
    {
        $this->configureSharedOptions($parameter, $nodeBuilder->booleanNode($parameter->name));
    }

    private function configureFloat(\ReflectionParameter $parameter, NodeBuilder $nodeBuilder): void
    {
        $this->configureSharedOptions($parameter, $nodeBuilder->floatNode($parameter->name));
    }

    private function configureArray(\ReflectionParameter $parameter, string $docComment, NodeBuilder $nodeBuilder): void
    {
        $arrayItemType = $this->getArrayItemType($parameter, $docComment);
        $arrayNode = $nodeBuilder->arrayNode($parameter->name);

        switch ($arrayItemType) {
            case 'int':
                $arrayNode->integerPrototype();
                break;
            case 'string':
                $arrayNode->scalarPrototype();
                break;
            case 'bool':
                $arrayNode->booleanPrototype();
                break;
            case 'float':
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

    private function getArrayItemType(\ReflectionParameter $parameter, string $docComment): string
    {
        $outputArray = [];
        $success = preg_match('/@param\s*(array|iterable)<(.*,)?\s*(.+)>(\|null)?\s*\$'.$parameter->name.'/', $docComment, $outputArray);

        if ((bool) $success !== true) {
            throw new \LogicException('Constructor parameter "'.$parameter->name.'" must have a PHPDoc annotation in the following format "@param array<T> $'.$parameter->name.'" where T is a nested type.');
        }

        // TODO: assert preg match success
        // TODO: assert that it's a valid item type
        return $outputArray[3];
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
