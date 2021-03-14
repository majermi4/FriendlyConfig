<?php

declare(strict_types=1);

namespace majermi4\FriendlyConfig;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class FriendlyConfiguration implements ConfigurationInterface
{
    private TreeBuilder $treeBuilder;

    private function __construct(TreeBuilder $treeBuilder)
    {
        $this->treeBuilder = $treeBuilder;
    }

    /**
     * @param class-string $class
     */
    public static function fromClass(string $class, string $rootNodeName): ConfigurationInterface
    {
        $treeBuilder = new TreeBuilder($rootNodeName);

        $configureTreeBuilder = new ConfigureTreeBuilder($class);
        $configureTreeBuilder($treeBuilder);

        return new self($treeBuilder);
    }

    public function getConfigTreeBuilder(): TreeBuilder
    {
        return $this->treeBuilder;
    }
}
