<?php

declare(strict_types=1);

namespace Majermi4\FriendlyConfig\Tests\Util;

use ArrayAccess;

class BaseTestConfig implements ArrayAccess
{
    /**
     * @var array<string,mixed>
     */
    public array $configValues = [];

    public function __construct(...$args)
    {
        $childClassReflection = new \ReflectionClass($this);
        $constructor = $childClassReflection->getConstructor();
        $parameters = $constructor->getParameters();

        foreach ($args as $i => $arg) {
            $this[$parameters[$i]->name] = $arg;
        }
    }

    public function offsetExists($offset): bool
    {
        return \array_key_exists($offset, $this->configValues);
    }

    public function offsetGet($offset)
    {
        return $this->configValues[$offset];
    }

    public function offsetSet($offset, $value): void
    {
        $this->configValues[$offset] = $value;
    }

    public function offsetUnset($offset): void
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

            if ($other[$key] !== $value) {
                return false;
            }
        }

        return true;
    }
}
