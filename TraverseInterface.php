<?php

namespace MaplePHP\DTO;

use BadMethodCallException;
use ErrorException;
use ReflectionException;

interface TraverseInterface
{

    /**
     * Object traverser
     * @param $key
     * @return Traverse|null
     */
    public function __get($key);

    /**
     * Immutable formating class
     * @param $method
     * @param $args
     * @return self
     * @throws ReflectionException|BadMethodCallException
     */
    public function __call($method, $args);

    /**
     * Validate current item and set to fallback (default: null) if not valid
     * @param string $method
     * @param array $args
     * @return bool
     * @throws ErrorException|BadMethodCallException
     */
    public function valid(string $method, array $args): bool;

    /**
     * With new object
     * @param mixed $data
     * @return $this
     */
    public function with(mixed $data): self;

    /**
     * Add a data to new object column/name
     * @param string $columnName The new column name
     * @param mixed  $value      The added value
     */
    public function add(string $columnName, mixed $value): self;

    /**
     * Get/return result
     * @param  string|null $fallback
     * @return mixed
     */
    public function get(?string $fallback = null): mixed;


    /**
     * Convert collection into an array
     * @param callable|null $callback
     * @return array
     */
    public function toArray(?callable $callback = null): array;


    /**
     * Immutable: Access incremental array
     * @param callable|null $callback Access array row in the callbacks argument
     * @return array|object|null
     */
    public function fetch(?callable $callback = null): array|object|null;

    /**
     * Count if row is array. Can be used to validate before @fetch method
     * @return int
     */
    public function count(): int;


    /**
     * Isset
     * @return mixed
     */
    public function isset(): mixed;

    /**
     * Create a fallback value if value is Empty/Null/0/false
     * @param  string $fallback
     * @return self
     */
    public function fallback(mixed $fallback): self;
}