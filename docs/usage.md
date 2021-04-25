# :seedling: Friendly Config - Usage

One of the goals for this library is that a developer should be able to use it without needing to read any documentation. If you know how to write PHP objects, you should not need more than the [basics](#basics) to get started.

However, if this approach feels as too much magic :sparkles:, feel free to read these docs from start to finish. That way, you should learn that all that may seem as magic is just bunch of simple tricks. :wink:

## Basics

TODO

## Simple types



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

## Nested object



## Array of nested objects
...

...