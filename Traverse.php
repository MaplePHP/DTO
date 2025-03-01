<?php

/**
 * @Package:    MaplePHP - The main traverse class
 * @Author:     Daniel Ronkainen
 * @Licence:    Apache-2.0 license, Copyright © Daniel Ronkainen
                Don't delete this comment, its part of the license.
 */

namespace MaplePHP\DTO;

use BadMethodCallException;
use ErrorException;
use MaplePHP\DTO\Format\Str;
use MaplePHP\Validate\Inp;
use ReflectionClass;
use ReflectionException;
use stdClass;

use function MaplePHP\DTO\helpers\debug_dump;
use function MaplePHP\DTO\helpers\traversArrFromStr;

/**
 * @method arr()
 * @method clock()
 * @method dom()
 * @method encode()
 * @method local()
 * @method num()
 * @method str()
 * @mixin Str
 */
class Traverse extends DynamicDataAbstract implements TraverseInterface
{
    use Traits\ArrayUtilities;

    protected mixed $raw = null; // Use raw to access current instance data (access array)

    public function __construct(mixed $data = null)
    {
        parent::__construct();
        $this->build($data);
    }

    /**
     * Init instance
     *
     * @param mixed $data
     * @return self
     */
    public static function value(mixed $data): self
    {
        return new self($data);
    }

    /**
     * With new object
     *
     * @param mixed $data
     * @return $this
     */
    public function with(mixed $data): self
    {
        return new self($data);
    }

    /**
     * Object traverser
     *
     * @param $key
     * @return Traverse|null
     */
    public function __get($key)
    {
        if(isset($this->getData()->{$key})) {
            $data = $this->getData()->{$key};
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
     *
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
     * Will return array item at index/key
     * https://www.php.net/manual/en/function.shuffle.php
     *
     * @param int|float|string $key
     * @return mixed    Will return false if key is missing
     */
    public function eq(int|float|string $key): mixed
    {
        if (is_string($key) && str_contains($key, ".")) {
            return traversArrFromStr($this->toArray(), $key);
        }
        return ($this->raw[$key] ?? false);
    }

    /**
     * Get first item in collection
     * https://www.php.net/manual/en/function.reset.php
     *
     * @return mixed
     */
    public function first(): mixed
    {
        if(!is_array($this->raw)) {
            return (string)$this->raw;
        }
        return reset($this->raw);
    }

    /**
     * Get last item in collection
     * https://www.php.net/manual/en/function.end.php
     *
     * @return mixed
     */
    public function last(): mixed
    {
        if(!is_array($this->raw)) {
            return (string)$this->raw;
        }
        return end($this->raw);
    }

    /**
     * Searches the array for a given value and returns the first corresponding key if successful
     * https://www.php.net/manual/en/function.array-search.php
     *
     * @param mixed $needle
     * @param bool $strict
     * @return string|int|false
     */
    public function search(mixed $needle, bool $strict = false): string|int|false
    {
        return array_search($needle, $this->raw, $strict);
    }

    /**
     * Will add item to object and method chain
     *
     * @param string $key  The object key name
     * @param mixed $value The object item value
     * @return self
     */
    public function add(string $key, mixed $value): self
    {
        $inst = clone $this;
        $inst->addToObject($key, $value);
        $inst->raw = $inst->getData()->{$key};
        return $inst;
    }

    /**
     * Validate current item and set to fallback (default: null) if not valid
     * @param string $method
     * @param array $args
     * @return bool
     * @throws ErrorException|BadMethodCallException
     */
    public function valid(string $method, array $args = []): bool
    {
        $inp = Inp::value($this->raw);
        if(!method_exists($inp, $method)) {
            throw new BadMethodCallException("The DTO valid method \"$method\" does not exist!", 1);
        }
        return $inp->{$method}(...$args);
    }

    /**
     * Same as value validate but will method chain.
     * If invalid then the value will be set to "null" OR whatever you set the fallback
     *
     * @param string $method
     * @param array $args
     * @param mixed|null $fallback
     * @return $this
     * @throws ErrorException
     */
    public function validChaining(string $method, array $args, mixed $fallback = null): self
    {
        if(!$this->valid($method, $args)) {
            $this->raw = $fallback;
        }
        return $this;
    }


    /**
     * Returns the JSON representation of a value
     * https://www.php.net/manual/en/function.json-encode.php
     *
     * @return self
     */
    public function toJson(mixed $value, int $flags = 0, int $depth = 512): mixed
    {
        return json_encode($value, $flags, $depth);
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
            $inst->raw = $inst->getData();
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

    public function each(callable $callback)
    {
        return $this->fetch($callback);
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
            $inst->raw = $inst->getData();
        }

        foreach ($inst->raw as $key => $row) {
            if (!is_null($callback)) {
                if (($get = $callback($inst::value($row), $key, $row, $index)) !== false) {
                    $new[$key] = $get;
                } else {
                    break;
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
     * Dump collection into a human-readable array dump
     *
     * @return void
     */
    public function dump(): void
    {
        debug_dump($this->toArray(), "Traverse");
    }

    /**
     * Count if row is array. Can be used to validate before @fetch method
     *
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
     * MOVE TO A CALCULATION LIBRARY
     */

    /**
     * Calculate the sum of values in an array
     * https://www.php.net/manual/en/function.array-sum.php
     *
     * @return float|int
     */
    public function sum(): float|int
    {
        $arr = $this->raw;
        if(!is_array($arr)) {
            $arr = $this->toArray();
        }
        return array_sum($arr);
    }
}
