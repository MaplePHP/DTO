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
use ReflectionException;
use stdClass;

/**
 * @method arr()
 * @method clock()
 * @method dom()
 * @method encode()
 * @method local()
 * @method num()
 * @method str()
 */
class Traverse extends DynamicDataAbstract
{
    protected mixed $raw = null; // Use raw to access current instance data (access array)
    protected $data = null;

    public function __construct(mixed $data = null)
    {
        parent::__construct();
        $this->build($data);
    }

    /**
     * With new object
     * @param mixed $data
     * @return $this
     */
    public function with(mixed $data): self
    {
        return new self($data);
    }

    /**
     * Object traverser
     * @param $key
     * @return Traverse|null
     */
    public function __get($key)
    {
        if(isset($this->data->{$key})) {
            $data = $this->data->{$key};
            if(is_object($data) && !($data instanceof DynamicDataAbstract)) {
                return $data;
            }
            return $this::value($data);
        }

        if(isset($this->raw[$key]) || isset($this->raw->{$key})) {
            return $this::value($this->raw[$key] ?? $this->raw->{$key});
        }

        $this->raw = null;
        return $this;
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
        //$data = [$method];
        $expectedClass = array_shift($data);
        $formatClassInst = $this->format($expectedClass, $this->raw);
        $expectedMethod = implode('', $data);
        if(!$expectedMethod) {
            return $formatClassInst;
        }
        $expectedMethod = lcfirst($expectedMethod);

        if(!method_exists($formatClassInst, $expectedMethod) &&
            ($formatClassInst === "Collection" && !function_exists($expectedMethod))) {
            throw new BadMethodCallException("The DTO method \"$expectedMethod\" does not exist!", 1);
        }

        $select = $formatClassInst->{$expectedMethod}(...$args);
        $inst->raw = (method_exists($select, "get")) ? $select->get() : $select;
        return $inst;
    }

    /**
     * Get/return result
     * @param  string|null $fallback
     * @return mixed
     */
    public function get(?string $fallback = null): mixed
    {
        return ($this->raw ?? $fallback);
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

    /**
     * Validate current item and set to fallback (default: null) if not valid
     * @param string $method
     * @param array $args
     * @param mixed|null $fallback
     * @return $this
     */
    public function valid(string $method, array $args, mixed $fallback = null): self
    {
        $inp = Inp::value($this->raw);
        if(!$inp->{$method}(...$args)) {
            $this->raw = $fallback;
        }
        return $this;
    }

    /**
     * Json decode value
     * @return self
     */
    public function jsonDecode(): self
    {
        $this->raw = json_decode($this->raw);
        return $this::value($this->raw);
    }

    /**
     * Convert collection into an array
     * @param callable|null $callback
     * @return array
     */
    public function toArray(?callable $callback = null): array
    {
        $index = 0;
        $new = [];
        $inst = clone $this;

        if (is_null($inst->raw)) {
            $inst->raw = $inst->data;
        }

        if(!is_object($inst->raw) && !is_array($inst->raw)) {
            $inst->raw = [$inst->raw];
        }

        foreach ($inst->raw as $key => $row) {

            if (is_callable($callback) &&
                (($get = $callback($row, $key, $index)) !== false)) {
                $row = $get;
            }

            if($row instanceof self) {
                $row = $row->get();
            }

            $new[$key] = $row;
            $index++;
        }
        return $new;
    }

    /**
     * Immutable: Access incremental array
     * @param callable|null $callback Access array row in the callbacks argument
     * @return array|object|null
     */
    public function fetch(?callable $callback = null): array|object|null
    {
        $index = 0;
        $new = [];
        $inst = clone $this;

        if (is_null($inst->raw)) {
            $inst->raw = $inst->data;
        }

        foreach ($inst->raw as $key => $row) {
            if (!is_null($callback)) {
                if (($get = $callback($inst::value($inst->raw), $row, $key, $index)) !== false) {
                    $new[$key] = $get;
                }
            } else {
                if (is_array($row) || ($row instanceof stdClass)) {
                    // Incremental -> object
                    $value = $inst::value($row);
                } elseif (is_object($row)) {
                    $value = $row;
                } else {
                    // Incremental -> value
                    $value = !is_null($row) ? Format\Str::value($row) : null;
                }
                $new[$key] = $value;
            }
            $index++;
        }

        $inst->raw = $new;
        return $inst->raw;
    }

    /**
     * Check if current traverse data is equal to val
     * @param  string $isVal
     * @return bool
     */
    public function equalTo(string $isVal): bool
    {
        return ($this->raw === $isVal);
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
        return (isset($this->raw)) ? $this->raw : false;
    }

    /**
     * Create a fallback value if value is Empty/Null/0/false
     * @param  string $fallback
     * @return self
     */
    public function fallback(mixed $fallback): self
    {
        if (!$this->raw) {
            $this->raw = $fallback;
        }
        return $this;
    }

    /**
     * Sprint over values
     * @param  string $add
     * @return self
     */
    public function sprint(string $add): self
    {
        if (!is_null($this->raw)) {
            $this->raw = sprintf($add, $this->raw);
        }
        return $this;
    }

    /**
     * Applies the callback to the elements of the given arrays
     * https://www.php.net/manual/en/function.array-map.php
     * @param  callable $callback  A callable to run for each element in each array. 
     * @param  array    $array    Supplementary variable list of array arguments
     * @return self
     */
    public function map(callable $callback, array ...$array): self
    {
        $this->raw = array_map($callback, $this->fetch(), ...$array);
        return $this;
    }

    /**
     * Filters elements of an array using a callback function
     * https://www.php.net/manual/en/function.array-filter.php
     * @param  callable|null $callback   The callback function to use
     *                                   If no callback is supplied, all empty entries of array will be 
     *                                   removed. See empty() for how PHP defines empty in this case. 
     * @param  int|integer   $mode       Flag determining what arguments are sent to callback: 
     * @return self
     */
    public function filter(?callable $callback = null, int $mode = 0): self
    {
        $data = is_null($callback) ? $this->raw : $this->fetch();
        $this->raw = array_filter($data, $callback, $mode);
        return $this;
    }

    /**
     * Iteratively reduce the array to a single value using a callback function
     * https://www.php.net/manual/en/function.array-reduce.php
     * @param  callable   $callback
     * @param  mixed|null $initial
     * @return self
     */
    public function reduce(callable $callback, mixed $initial = null): self
    {
        $this->raw = array_reduce($this->fetch(), $callback, $initial);
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
        $className = "MaplePHP\\DTO\\Format\\$name";
        if (!class_exists($className)) {
            throw new BadMethodCallException("The DTO class \"$dtoClassName\" does not exist!", 1);
        }

        $reflect = new ReflectionClass($className);
        $instance = $reflect->newInstanceWithoutConstructor();
        return $instance->value($value);
    }

    /**
     * Build the object
     * @param mixed $data
     * @return $this
     */
    protected function build(mixed $data): self
    {
        if (is_array($data) || is_object($data)) {
            foreach ($data as $k => $v) {
                $this->{$k} = $v;
            }
        }
        $this->raw = $data;
        return $this;
    }

    /**
     * Init instance
     * @param mixed $data
     * @return self
     */
    public static function value(mixed $data): self
    {
        return new self($data);
    }
}
