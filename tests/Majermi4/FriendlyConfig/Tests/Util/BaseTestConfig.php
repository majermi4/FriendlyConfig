<?php

declare(strict_types=1);

namespace Majermi4\FriendlyConfig\Tests\Util;

use ArrayAccess;
use Webmozart\Assert\Assert;

class BaseTestConfig implements ArrayAccess
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
        Assert::notNull($constructor, 'Children classes are expected to have a constructor.');
        $parameters = $constructor->getParameters();

        foreach ($args as $i => $arg) {
            $this[$parameters[$i]->name] = $arg;
        }
    }

    /**
     * @param mixed $offset
     */
    public function offsetExists($offset): bool
    {
        return \array_key_exists($offset, $this->configValues);
    }

    /**
     * @param mixed $offset
     *
     * @return mixed
     */
    public function offsetGet($offset)
    {
        return $this->configValues[$offset];
    }

    /**
     * @param mixed $offset
     * @param mixed $value
     */
    public function offsetSet($offset, $value): void
    {
        $this->configValues[$offset] = $value;
    }

    /**
     * @param mixed $offset
     */
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

            if (\is_array($value) && \is_array($other[$key]) && \count($value) === \count($other[$key])) {
                foreach ($value as $idx => $nestedValue) {
                    if ($nestedValue instanceof self && $other[$key][$idx] instanceof self) {
                        if (!$nestedValue->equals($other[$key][$idx])) {
                            return false;
                        }

                        continue;
                    }

                    if ($other[$key][$idx] !== $nestedValue) {
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
