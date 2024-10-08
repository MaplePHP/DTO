<?php

/**
 * @Package:    MaplePHP Dynamic data abstraction Class
 * @Author:     Daniel Ronkainen
 * @Licence:    Apache-2.0 license, Copyright © Daniel Ronkainen
                Don't delete this comment, its part of the license.
 */

namespace MaplePHP\DTO\Format;

use InvalidArgumentException;
use ReflectionClass;
use ReflectionException;

abstract class FormatAbstract
{
    protected $value;

    public function __construct(mixed $value)
    {
        $this->value = $value;
    }

    /**
     * Get DTO value
     * @return mixed
     */
    public function get(): mixed
    {
        return $this->value;
    }

    /**
     * Set a fallback value if current value is empty
     * @param  string $fallback
     * @return self
     */
    public function fallback(string $fallback): self
    {
        if (!$this->value) {
            $this->value = $fallback;
        }
        return $this;
    }

    /**
     * Clone data
     * @return static
     */
    public function clone(): self
    {
        return clone $this;
    }

    /**
     * Get Value
     * @return string
     */
    public function __toString(): string
    {
        return (string)$this->get();
    }

    /**
     * Sprit
     * @param  string $add
     * @return self
     */
    public function sprint(string $add): static
    {
        if (!is_null($this->value)) {
            $this->value = sprintf($add, $this->value);
        }
        return $this;
    }

    /**
     * Access and return format class object
     * @param string $dtoClassName The DTO format class name
     * @return object
     * @throws ReflectionException
     */
    public function dto(string $dtoClassName): object
    {
        $name = ucfirst($dtoClassName);
        $className = "MaplePHP\\DTO\\Format\\$name";
        if (!class_exists($className)) {
            throw new InvalidArgumentException("The DTO Format class do not exist!", 1);
        }
        $reflect = new ReflectionClass($className);
        $instance = $reflect->newInstanceWithoutConstructor();
        return $instance->value($this->value);
    }
}
