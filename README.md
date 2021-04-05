# :seedling: Friendly Config _(Easy way to write Symfony Configuration)_

> Provides a friendlier way to define [Symfony configuration](https://symfony.com/doc/current/components/config/definition.html) using plain old PHP objects.

[![Latest Stable Version](https://poser.pugx.org/majermi4/friendly-config/v)](//packagist.org/packages/majermi4/friendly-config)
![tests](https://github.com/majermi4/FriendlyConfig/actions/workflows/php.yml/badge.svg)
[![Coverage Status](https://coveralls.io/repos/github/majermi4/FriendlyConfig/badge.svg?branch=main)](https://coveralls.io/github/majermi4/FriendlyConfig?branch=main)
[![License: MIT](https://img.shields.io/badge/License-MIT-blue.svg)](https://github.com/majermi4/FriendlyConfig/blob/main/LICENSE)

We love the Symfony configuration component! :heart: It provides schema, validation, documentation and many more features to our bundle configs. However, many of us don't like defining it ... The goal of this project is to change that.

## Documentation

1. [Motivation](./docs/motivation.md)
    1. [Code duplication](./docs/motivation.md#code-duplication)
    2. [Writing config definition manually is hard](./docs/motivation.md#writing-config-definition-manually-is-hard)
    3. [Negative impact on development and refactoring speed](./docs/motivation.md#negative-impact-on-development-and-refactoring-speed)
1. [Usage](./docs/usage.md)
    1. [Basics](./docs/usage.md#basics)
    1. [Simple types](./docs/usage.md#simple-types)
    1. [Nested types](./docs/usage.md)
    1. [Other inferred configuration options](./docs/usage.md)
       1. [Required](./docs/usage.md)
       1. [Default value](./docs/usage.md)
       1. [Info](./docs/usage.md)
    1. [Symfony DI component integration](./docs/usage.md)
1. [Conventions & Limitations](./docs/conventions_and_limitations.md)

## Installation

This is installable via [Composer](https://getcomposer.org/) as
[majermi4/friendly-config](https://packagist.org/packages/majermi4/friendly-config):

    composer require majermi4/friendly-config

## Basic example:

Instead of having to write configuration such as this:

```php
$rootNode
    ->children()
        ->arrayNode('connection')
            ->children()
                ->scalarNode('driver')
                    ->isRequired()
                    ->cannotBeEmpty()
                ->end()
                ->scalarNode('host')
                    ->defaultValue('localhost')
                ->end()
                ->scalarNode('username')->end()
                ->scalarNode('password')->end()
                ->booleanNode('memory')
                    ->defaultFalse()
                ->end()
            ->end()
        ->end()
        ->arrayNode('settings')
            ->addDefaultsIfNotSet()
            ->children()
                ->scalarNode('name')
                    ->isRequired()
                    ->cannotBeEmpty()
                    ->defaultValue('value')
                ->end()
            ->end()
        ->end()
    ->end()
;
```

Write plain old PHP objects such as:

```php
class MyConfig
{
    public function __construct(Connection $connection, Settings $settings) { /* your code */ }
}

class Connection
{
    public function __construct(
        string $driver,
        string $username,
        string $password,
        string $host = 'localhost',
        bool $memory = false,
    ) { /* your code */ }
}

class Settings
{
    public function __construct(string $name = 'value') { /* your code */ }
}
```

The following few lines will convert your pure PHP objects into valid Symfony configuration that defines schema of your bundle configuration. On top of that, the processed configuration values are used to initialize your pure PHP objects so you can easily access the processed values. 

You can register the initialised config objects as services which will allow you to easily access the initialised config objects anywhere in your application.

```php
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Majermi4\FriendlyConfig\FriendlyConfiguration;
use Majermi4\FriendlyConfig\InitializeConfigObject;
use Majermi4\FriendlyConfig\RegisterConfigService;

class MyBundleExtension extends Extension
{
    /**
     * {@inheritdoc}
     */
    public function load(array $configs, ContainerBuilder $container): void
    {
        $configuration = $this->getConfiguration($configs, $container);
        $config = $this->processConfiguration($configuration, $configs);

        if ($configuration instanceof FriendlyConfiguration) {
            // Register config object with processed values as a service 
            RegisterConfigService::fromProcessedConfig($configuration->getConfigClass(), $config, $container);
    
            // Or ... initialise config object from processed config immediately if needed
            $initialisedConfig = InitializeConfigObject::fromProcessedConfig(MyConfig::class, $config);
        }        
    }
    
    /**
     * {@inheritdoc}
     */
    public function getConfiguration(array $config, ContainerBuilder $container) : ConfigurationInterface
    {
        return FriendlyConfiguration::fromClass(MyConfig::class, 'my_config');
    }
}
```

