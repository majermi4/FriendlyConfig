<?php

declare(strict_types=1);

namespace Majermi4\FriendlyConfig\Tests\Util;

use ArrayAccess;

/**
 * @implements ArrayAccess<string,mixed>
 */
class BaseTestConfig implements \ArrayAccess
{
    /**
     * @var array<string,mixed>
     */
    public array $configValues = [];

    /**
     * @param mixed ...$args
     */
    public function __construct(...$args)
    {
        $childClassReflection = new \ReflectionClass($this);
        $constructor = $childClassReflection->getConstructor();

        if ($constructor === null) {
            throw new \RuntimeException('Children classes must define constructor.');
        }

        $parameters = $constructor->getParameters();

        foreach ($args as $i => $arg) {
            $this[$parameters[$i]->name] = $arg;
        }
    }

    public function offsetExists(mixed $offset): bool
    {
        return \array_key_exists($offset, $this->configValues);
    }

    public function offsetGet(mixed $offset): mixed
    {
        return $this->configValues[$offset];
    }

    public function offsetSet(mixed $offset, mixed $value): void
    {
        $this->configValues[$offset] = $value;
    }

    public function offsetUnset(mixed $offset): void
    {
        unset($this->configValues[$offset]);
    }

    public function equals(self $other): bool
    {
        if (\count($this->configValues) !== \count($other->configValues)) {
            return false;
        }

        foreach ($this->configValues as $key => $value) {
            if ($value instanceof self && $other[$key] instanceof self) {
                if (!$value->equals($other[$key])) {
                    return false;
                }

                continue;
            }

            if (\is_array($value) && \is_array($other[$key]) && \count($value) === \count($other[$key])) {
                foreach ($value as $idx => $nestedValue) {
                    if ($nestedValue instanceof self && $other[$key][$idx] instanceof self) {
                        if (!$nestedValue->equals($other[$key][$idx])) {
                            return false;
                        }

                        continue;
                    }

                    if (!isset($other[$key][$idx]) || $other[$key][$idx] !== $nestedValue) {
                        return false;
                    }
                }

                continue;
            }

            if ($other[$key] !== $value) {
                return false;
            }
        }

        return true;
    }
}
