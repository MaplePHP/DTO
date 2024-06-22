<?php

/**
 * @Package:    MaplePHP - The main traverse class
 * @Author:     Daniel Ronkainen
 * @Licence:    Apache-2.0 license, Copyright © Daniel Ronkainen
                Don't delete this comment, its part of the license.
 */

namespace MaplePHP\DTO;

use BadMethodCallException;
use MaplePHP\DTO\Format\Str;
use MaplePHP\Validate\Inp;
use ReflectionClass;
use InvalidArgumentException;
use ReflectionException;
use stdClass;

class Traverse extends DynamicDataAbstract
{
    protected $row; // Use row to access current instance (access inst/object)
    protected $raw; // Use raw to access current instance data (access array)
    protected $data = null;

    /**
     * Object traverser
     * @param $key
     * @return Traverse|null
     */
    public function __get($key)
    {
        if(isset($this->data->{$key}) || isset($this->data[$key])) {
            return $this::value($this->data[$key]);
        }
        return null;
    }

    /**
     * Immutable formating class
     * @param $method
     * @param $args
     * @return self
     * @throws ReflectionException|BadMethodCallException
     */
    public function __call($method, $args)
    {
        $inst = clone $this;
        $data = Str::value($method)->camelCaseToArr()->get();
        $expectedClass = array_shift($data);
        $expectedMethod = implode('', $data);
        $expectedMethod = lcfirst($expectedMethod);
        $formatClassInst = $this->format($expectedClass, $this->row);
        if(!method_exists($formatClassInst, $expectedMethod)) {
            throw new BadMethodCallException("The DTO method \"$expectedMethod\" does not exist!", 1);
        }
        $inst->row = $formatClassInst->{$expectedMethod}(...$args)->get();
        return $inst;
    }

    /**
     * Get/return result
     * @param  string|null $fallback
     * @return mixed
     */
    public function get(?string $fallback = null): mixed
    {
        return (!is_null($this->row) ? $this->row : $fallback);
    }

    /**
     * Get raw
     * @return mixed
     */
    public function getRaw(): mixed
    {
        return $this->raw;
    }

    /**
     * Add a data to new object column/name
     * @param string $columnName The new column name
     * @param mixed  $value      The added value
     */
    public function add(string $columnName, mixed $value): self
    {
        $this->{$columnName} = $value;
        return $this;
    }

    public function valid(string $method, array $args, mixed $fallback = null): self
    {
        $inp = Inp::value($this->raw);
        if(!$inp->{$method}(...$args)) {
            $this->raw = $this->row = $fallback;
        }
        return $this;
    }

    /**
     * Json decode value
     * @return self
     */
    public function jsonDecode(): self
    {
        $this->row = json_decode($this->row);
        return $this::value($this->row);
    }

    /**
     * Immutable: Access incremental array
     * @param callable|null $callback Access array row in the callbacks argument
     * @return array|object|null
     */
    public function fetch(?callable $callback = null): array|object|null
    {
        $index = 0;
        $new = array();
        $inst = clone $this;

        if (is_null($inst->row)) {
            $inst->row = $inst->data;
        }

        foreach ($inst->row as $key => $row) {
            if (!is_null($callback)) {
                if (($get = $callback($inst::value($inst->row), $row, $key, $index)) !== false) {
                    $new[$key] = $get;
                }
            } else {
                if (is_array($row) || (is_object($row) && ($row instanceof stdClass))) {
                    // Incremental -> object
                    $value = $inst::value($row);
                } elseif (is_object($row)) {
                    $value = $row;
                } else {
                    // Incremental -> value
                    $value = Format\Str::value($row);
                }

                $new[$key] = $value;
            }
            $index++;
        }

        $inst->row = $new;
        return $inst->row;
    }

    /**
     * Chech if current traverse data is equal to val
     * @param  string $isVal
     * @return bool
     */
    public function equalTo(string $isVal): bool
    {
        return ($this->row === $isVal);
    }

    /**
     * Count if row is array. Can be used to validate before @fetch method
     * @return int
     */
    public function count(): int
    {
        return (is_array($this->raw) ? count($this->raw) : 0);
    }

    /**
     * Isset
     * @return mixed
     */
    public function isset(): mixed
    {
        return (isset($this->raw)) ? $this->row : false;
    }

    /**
     * Create a fallback value if value is Empty/Null/0/false
     * @param  string $fallback
     * @return self
     */
    public function fallback(mixed $fallback): self
    {
        if (!$this->row) {
            $this->row = $fallback;
        }
        return $this;
    }

    /**
     * Sprint over values
     * @param  string $add
     * @return self
     */
    public function sprint(string $add)
    {
        if (!is_null($this->raw)) {
            $this->row = sprintf($add, $this->row);
        }
        return $this;
    }

    /**
     * Access and return format class object
     * @param string $dtoClassName The DTO format class name
     * @param mixed $value
     * @return object
     * @throws ReflectionException|BadMethodCallException
     */
    protected function format(string $dtoClassName, mixed $value): object
    {
        $name = ucfirst($dtoClassName);
        $className = "MaplePHP\\DTO\\Format\\{$name}";
        if (!class_exists($className)) {
            throw new BadMethodCallException("The DTO class \"$dtoClassName\" does not exist!", 1);
        }
        $reflect = new ReflectionClass($className);
        $instance = $reflect->newInstanceWithoutConstructor();
        return $instance->value($value);
    }

    /**
     * Init instance
     * @param mixed $data
     * @param null $raw
     * @return self
     */
    public static function value(mixed $data, $raw = null): self
    {
        $inst = new self();
        $inst->raw = $raw;
        if (is_array($data) || is_object($data)) {
            foreach ($data as $k => $v) {
                $inst->raw = $inst->data[$k] = $inst->{$k} = $v;
            }
        } else {
            $inst->raw = $inst->row = $data;
        }
        return $inst;
    }
}
