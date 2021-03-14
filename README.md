# :seedling: Friendly Config _(Easy way to write Symfony Configuration)_

> Provides a friendlier way to define [Symfony configuration](https://symfony.com/doc/current/components/config/definition.html) using plain old PHP objects.

![tests](https://github.com/majermi4/friendly-config/actions/workflows/php.yml/badge.svg)

> :construction: This project is currently in progress, not suited for production yet :construction:

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

The following few lines will convert your pure PHP objects into valid Symfony configuration that defines schema of your bundle configuration. On top of that, the processed configuration values are used to initialize your pure PHP objects so you can easily access the configured values. You can choose to register the initialised config objects as services which will allow you to easily access the initialised config objects anywhere in your application.

```php
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use majermi4\FriendlyConfig\FriendlyConfiguration;
use majermi4\FriendlyConfig\InitializeConfigObject;
use majermi4\FriendlyConfig\InitializeConfigServiceDefinition;

class MyBundleExtension extends Extension
{
    public function load(array $configs, ContainerBuilder $container): void
    {
        $config = $this->processConfiguration(FriendlyConfiguration::fromClass(MyConfig::class), $configs);
        
        // Initialise config object from processed config
        $initialisedConfig = InitializeConfigObject::fromProcessedConfig(MyConfig::class, $config);

        // Register config object with processed values as a service 
        $configServiceDefinition = InitializeConfigServiceDefinition::fromProcessedConfig(MyConfig::class, $config);
        $container->addDefinitions([$configServiceDefinition]);
    }
}
```

