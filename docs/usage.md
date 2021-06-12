# :seedling: Friendly Config - Usage

The goal of this project is that a developer should be able to use it without needing to read any documentation. If you know how to write PHP objects, you should not need more than the [basics](#basics) to get started.

However, if this approach feels as too much magic :sparkles:, feel free to read these docs from start to finish. That way, you should learn that all that may seem as magic is just bunch of simple tricks. :wink:

## Basics

Until now, we were required to write `Configuration` definition class as [explained in the official Symfony docs](https://symfony.com/doc/current/components/config/definition.html) and learn how to use the TreeBuilder to create configuration such as this:

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

Instead, we can write plain old PHP objects such as:

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

The following few lines will convert your pure PHP objects into valid Symfony configuration that defines schema of your bundle configuration. On top of that, the processed configuration values are used to initialize your pure PHP objects, so you can easily access the processed values.

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
    public function getConfiguration(array $config, ContainerBuilder $container) : ConfigurationInterface
    {
        return FriendlyConfiguration::fromClass(MyConfig::class, 'my_config');
    }
    
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
}
```

## Simple types

When you pass your classes to FriendlyConfig, it analyses their constructor parameters and generates the Symfony configuration definition for you. The following sections show how different parameter types get interpreted.

### Boolean

```php
public function __construct(bool $yesOrNo) { }
```

```php
$nodeBuilder
    ->booleanNode('yes_or_no')
        ->required()
    ->end();
```

### Integer

```php
public function __construct(int $number) { }
```

```php
$nodeBuilder
    ->integerNode('number')
        ->required()
    ->end();
```

### Float

```php
public function __construct(float $number) { }
```

```php
$nodeBuilder
    ->floatNode('number')
        ->required()
    ->end();
```

### String

```php
public function __construct(string $text) { }
```

```php
$nodeBuilder
    ->scalarNode('text')
        ->required()
    ->end();
```

## Nested types

### Array of booleans, integers, floats, strings

Friendly Config object:
```php
/**
 * @param array<bool>   $arrayOfBooleans
 * @param array<int>    $arrayOfIntegers
 * @param array<float>  $arrayOfFloats
 * @param array<string> $arrayOfStrings
 */
public function __construct(
    array $arrayOfBooleans,
    array $arrayOfIntegers,
    array $arrayOfFloats,
    array $arrayOfStrings
) { }
```

Symfony Config:
```php
$nodeBuilder
    ->arrayNode('array_of_booleans')
        ->booleanPrototype()
        ->required()
    ->end()
    ->arrayNode('array_of_integers')
        ->integerPrototype()
        ->required()
    ->end()
    ->arrayNode('array_of_floats')
        ->floatPrototype()
        ->required()
    ->end()
    ->arrayNode('array_of_strings')
        ->scalarPrototype()
        ->required()
    ->end();
```

Config value example:
```yaml
array_of_booleans: [ true, false, true, false ]
array_of_integers: [ 1, 2, 3, 4 ]
array_of_floats: [ 1.5, 2.5, 3.5, 4.5 ]
array_of_strings: [ "foo", "bar" ]
```

### Nested object

Friendly Config object:
```php
// Root config class constructor
public function __construct(NestedConfig $nestedConfig) { }

// Nested config class
class NestedConfig {
    public function __construct(string $nestedOption) { }
}
```

Symfony Config:
```php
$nodeBuilder
    ->arrayNode('nestedConfig')
        ->arrayPrototype()
            ->children()
                ->scalarNode('nestedOption')
                    ->required()
                ->end()
            ->end()
        ->end()
    ->end();
```

Config value example:
```yaml
nestedConfig:
    nestedOption: 'some value'
```

### Array of nested objects

Friendly Config object:
```php
// Root config class constructor
/**
 * @param array<string,NestedConfig> $nestedConfigs
 */
public function __construct(array $nestedConfigs) { }

// Nested config class
class NestedConfig {
    public function __construct(string $nestedOption) { }
}
```

Symfony Config:
```php
$nodeBuilder
    ->arrayNode('nestedConfigs')
        ->useAttributeAsKey('name')
        ->arrayPrototype()
            ->children()
                ->scalarNode('nestedOption')
                    ->required()
                ->end()
            ->end()
        ->end()
    ->end();
```

Config value example:
```yaml
nestedConfigs:
    nestedConfig1:
        nestedOption: 'some value'
    nestedConfig2:
        nestedOption: 'another value'
```

Optionally, you can skip the "key" part of the array param definition:

From: `@param array<string,NestedConfig> $nestedConfigs`
To: `@param array<NestedConfig> $nestedConfigs`
(Alternatively: `@param NestedConfig[] $nestedConfigs`)

This removes the `->useAttributeAsKey('name')` part of the configuration definition which allows you to accept array items in the following format without the need to define the keys.

```yaml
nestedConfigs:
    - { nestedOption: 'some value' }
    - { nestedOption: 'another value' }
```

## Other inferred configuration options

### Default value

When you define default value for your constructor parameters such as: 

```php
public function __construct(string $text = 'Default value') { }
```

```php
$nodeBuilder
    ->scalarNode('text')
        ->defaultValue('Default value')
    ->end();
```

If you want to define a default value of a non-scalar parameter, you can make the parameter nullable and set the default manually inside the body of the constructor function.

### Required

When a constructor parameter is nullable, or it has a default value, it is not considered required. It is considered required in all other cases.

```php
public function __construct(
    string $requiredOption,
    ?string $nullableOption,
    string $optionWithDefault = 'some default'
) { }
```

```php
$nodeBuilder
    ->scalarNode('text')
        ->required()
    ->end()
    ->scalarNode('nullableOption')->end()
    ->scalarNode('text')
        ->defaultValue('Default value')
    ->end();
```

### Info

An important feature of the Symfony Configuration component is that it provides documentation for all available configuration options with the `config:dump-reference` command

When you write configuration class such as this:

```php
class MyConfig {
    /**
     * Option info can be also parsed from the PhpDoc comment above a property
     * named the same as the constructor option in the same class. 
     */
    private string $optionDocumentedViaProperty;

    /**
     * @param string $optionDocumentedViaParameter This is another option to document config option.
     */
    public function __construct(
        string $optionDocumentedViaProperty,
        string $optionDocumentedViaParameter
    ) { }
}
```

Both texts from either the `@param` annotation or from the comment above a class property named the same as the constructor parameter will be used as documentation via the `->info()` method.

```php
$nodeBuilder
    ->scalarNode('optionDocumentedViaProperty')
        ->info('Option info can be also parsed from the PhpDoc comment above a property named the same as the constructor option in the same class.')
        ->required()
    ->end()
    ->scalarNode('optionDocumentedViaParameter')
        ->info('This is another option to document config option.')
        ->required()
    ->end();
```