<?php

declare(strict_types=1);

namespace Majermi4\FriendlyConfig;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class FriendlyConfiguration implements ConfigurationInterface
{
    private TreeBuilder $treeBuilder;
    /**
     * @var class-string
     */
    private string $configClass;

    /**
     * @param class-string $configClass
     */
    private function __construct(TreeBuilder $treeBuilder, string $configClass)
    {
        $this->treeBuilder = $treeBuilder;
        $this->configClass = $configClass;
    }

    /**
     * @param class-string $class
     */
    public static function fromClass(string $class, string $rootNodeName): ConfigurationInterface
    {
        $treeBuilder = new TreeBuilder($rootNodeName);

        (new ConfigureTreeBuilder())($treeBuilder, $class);

        return new self($treeBuilder, $class);
    }

    public function getConfigTreeBuilder(): TreeBuilder
    {
        return $this->treeBuilder;
    }

    /**
     * @return class-string
     */
    public function getConfigClass(): string
    {
        return $this->configClass;
    }
}
