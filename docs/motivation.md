# :seedling: Friendly Config - Motivation

The idea for this project comes from a code base with dozens of re-usable Symfony bundles that heavily relies on [Symfony Config Component](https://symfony.com/doc/current/components/config.html) for specifying various options when those bundles are re-used in different projects.

As much as we love :heart: the Symfony config component, we noticed a few problems when using it on daily basis:

1. [Code duplication](#1-code-duplication)
2. [Writing config definition manually feels hard](#2-writing-config-definition-manually-feels-hard)
3. [Negative impact on development and refactoring speed](#3-negative-impact-on-development-and-refactoring-speed)

The **Friendly Config** library tries to solve these 3 problems by having a single source of truth (1) represented by simple PHP objects (POPO). This means that every PHP developer intuitively knows how to define a config definition (2) which leads to faster development of new features and refactoring of existing functionality (3).

## 1. Code duplication

When working with Symfony Config in large projects, it is convenient to load the configured values into PHP objects registered as services so that they can be accessed via the DI and relied on for strict static types.

For a simple config in this format:

```yaml
acme_bundle:
  some_option: "configured value"
```

we might write a class such as:

```php
class AcmeConfig 
{
    private string $someOption;
    
    public function __construct(string $someOption = 'some default') 
    {
        $this->someOption = $someOption;
    }
    
    public function getSomeOption() : string
    {
        return $this->someOption;
    }
}
```

Because of the strict type support in new PHP versions, this class basically contains all information needed to define the structure of the expected config. Because we always write this class anyways, it is very repetitive having to write config definitions such as:

```php
namespace Acme\SocialBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class Configuration implements ConfigurationInterface
{
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder('acme_bundle');

        $treeBuilder->getRootNode()
            ->children()
                ->integerNode('some_option')->end()
                ->required()
                ->defaultValue('some default')
            ->end()
        ;

        return $treeBuilder;
    }
}
```

## 2. Writing config definition manually feels hard

The example of Symfony config definition from the previous section is a very simple one. When a project grows and new nested options are added regularly, these configuration classes start to grow in complexity and even experienced developers have confessed to finding themselves re-visiting the docs or simply copy-pasting previously used definition structures from other places to avoid making mistakes and getting stuck. 

This is not to say that learning the config definition _[DSL](https://en.wikipedia.org/wiki/Domain-specific_language)_ is a rocket science. However, it requires familiarising yourself with a new syntax and concepts that do not necessarily add that much value.

## 3. Negative impact on development and refactoring speed

Combination of the previous 2 problems (code duplication and writing config definitions being hard) negatively impacts the development speed. What we found is that developers postpone definition of bundle configs until they are finished writing the actual feature logic, or they avoid making the feature configurable altogether because it feels heavy and as pre-mature optimisation. The reasons for that are understandable.

When developers write a new feature, the process is not linear. They iterate and discover possible new configuration options but also discard others that are no longer necessary. If defining configurable options feels heavy and there is a chance things will change, why not postpone it until later? First, every developer knows that non-essential tasks postponed for later have a tendency to not be implemented at all. Second, the moment when you discover a useful configuration option is exactly the time when it will be easier to define and describe it. 
