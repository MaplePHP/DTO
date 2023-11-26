<?php

/**
 * @Package:    MaplePHP - The main traverse class
 * @Author:     Daniel Ronkainen
 * @Licence:    Apache-2.0 license, Copyright © Daniel Ronkainen
                Don't delete this comment, its part of the license.
 */

namespace MaplePHP\DTO;

use MaplePHP\DTO\Format;

class Traverse extends DynamicDataAbstract
{
    protected $row; // Use row to access current instance (access inst/object)
    protected $raw; // Use raw to access current instance data (access array)
    protected $data = null;

    /**
     * Init intance
     * @param  mixed $data
     * @return self
     */
    public static function value(mixed $data, $raw = null): self
    {
        $inst = new self();
        $inst->raw = $raw;

        if (is_array($data) || is_object($data)) {
            foreach ($data as $k => $v) {
                $inst->data[$k] = $inst->{$k} = $v;
            }
        } else {
            $inst->raw = $inst->row = $data;
        }
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
     * Traverse factory
     * If you want
     * @return self
     */
    public function __call($method, $args)
    {
        $this->row = ($this->{$method} ?? null);
        $this->raw = $this->row;

        if (count($args) > 0) {
            $name = ucfirst($args[0]);
            $className = "MaplePHP\\DTO\\Format\\{$name}";
            if (!class_exists($className)) {
                throw new \Exception("The DTO Format class do not exist!", 1);
            }
            $reflect = new \ReflectionClass($className);
            $instance = $reflect->newInstanceWithoutConstructor();
            return $instance->value($this->row);
        }

        if (is_array($this->row) || is_object($this->row)) {
            return $this::value($this->row, $this->raw);
        }

        return self::value($this->row);
    }

    /**
     * Get raw
     * @return mixed
     */
    public function getRaw()
    {
        return $this->raw;
    }

    /**
     * Callable factory
     * @psalm-suppress InvalidFunctionCall Psalm do not understand that $call is callable
     * @return mixed
     */
    /*
    public function fetchFactory(): mixed
    {
        return function ($arr, $row, $_unusedKey, $index) {
            $data = array_values($this->raw);
            $call = (isset($data[$index])) ? $data[$index] : null;
            if (is_callable($call)) {
                return $call($arr, $row);
            }
            return false;
        };
    }
     */
    
    /**
     * Access incremental array
     * @param  callable|null $callback Access array row in the callbacks argumnet 1
     * @return self
     */
    public function fetch(?callable $callback = null)
    {
        $index = 0;
        $new = array();

        if (is_null($this->raw)) {
            $this->raw = $this->data;
        }

        foreach ($this->raw as $key => $row) {
            if (!is_null($callback)) {
                if (($get = $callback($this::value($this->raw), $row, $key, $index)) !== false) {
                    $new[$key] = $get;
                }
            } else {
                if (is_array($row) || (is_object($row) && ($row instanceof \stdClass))) {
                    // Incremental -> object
                    $value = $this::value($row);
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

        $this->row = $new;
        return $this;
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
    public function count()
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
     * @return mixed
     */
    public function fallback(mixed $fallback)
    {
        if (!$this->row) {
            $this->row = $fallback;
        }
        return $this;
    }

    /**
     * Sprit
     * @param  string $add
     * @return self
     */
    public function sprint(string $add)
    {
        if (!is_null($this->row)) {
            $this->row = sprintf($add, $this->row);
        }
        return $this;
    }
}
