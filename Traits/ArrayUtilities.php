<?php

namespace MaplePHP\DTO\Traits;

use Closure;
use MaplePHP\DTO\Traverse;
use MaplePHP\DTO\TraverseInterface;

trait ArrayUtilities
{
    /**
     * Applies the callback to the elements of the given arrays
     * https://www.php.net/manual/en/function.array-map.php
     *
     * @param callable $callback A callable to run for each element in each array.
     * @param array $array Supplementary variable list of array arguments
     * @return ArrayUtilities|Traverse
     */
    public function map(callable $callback, array ...$array): self
    {
        $this->raw = array_map($callback, $this->fetch(), ...$array);
        return $this;
    }

    /**
     * Filters elements of an array using a callback function
     * https://www.php.net/manual/en/function.array-filter.php
     *
     * @param callable|null $callback The callback function to use
     *                                   If no callback is supplied, all empty entries of array will be
     *                                   removed. See empty() for how PHP defines empty in this case.
     * @param int $mode Flag determining what arguments are sent to callback:
     * @return ArrayUtilities|Traverse
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
     *
     * @param callable $callback
     * @param mixed|null $initial
     * @return ArrayUtilities|Traverse
     */
    public function reduce(callable $callback, mixed $initial = null): self
    {
        $this->raw = array_reduce($this->fetch(), $callback, $initial);
        return $this;
    }

    /**
     * Split an array into chunks
     * https://www.php.net/manual/en/function.array-chunk.php
     *
     * @param int $length
     * @param bool $preserveKeys
     * @return Traverse|ArrayUtilities
     */
    public function chunk(int $length, bool $preserveKeys = false): self
    {
        $this->raw = array_chunk($this->fetch(), $length, $preserveKeys);
        return $this;
    }

    /**
     * Flatten a array
     *
     * @param bool $preserveKeys
     * @return Traverse|ArrayUtilities
     */
    public function flatten(bool $preserveKeys = false): self
    {
        $result = [];
        $array = $this->toArray();
        array_walk_recursive($array, function ($item, $key) use (&$result, $preserveKeys) {
            $item = $this->with($item);
            if ($preserveKeys) {
                $result[$key] = $item;
            } else {
                $result[] = $item;
            }
        });
        $this->raw = $result;
        return $this;
    }

    /**
     * Flatten array and preserve keys
     *
     * @return Traverse|ArrayUtilities
     */
    public function flattenWithKeys(): self
    {
        return $this->flatten(true);
    }

    /**
     * Merge two arrays
     *
     * @param array|TraverseInterface $combine
     * @param bool $before Merge before main collection
     * @return ArrayUtilities|Traverse
     */
    public function merge(array|TraverseInterface $combine, bool $before = false): self
    {
        $combine = $this->handleCollectArg($combine);
        if ($before) {
            $this->raw = array_merge($combine, $this->raw);
        } else {
            $this->raw = array_merge($this->raw, $combine);
        }
        return $this;
    }

    /**
     * Append array after main collection
     *
     * @param array|TraverseInterface $combine
     * @return ArrayUtilities|Traverse
     */
    public function append(array|TraverseInterface $combine): self
    {
        return $this->merge($combine);
    }

    /**
     * Perpend array after main collection
     *
     * @param array|TraverseInterface $combine
     * @return ArrayUtilities|Traverse
     */
    public function prepend(array|TraverseInterface $combine): self
    {
        return $this->merge($combine, true);
    }

    /**
     * Remove a portion of the array and replace it with something else
     * https://www.php.net/manual/en/function.array-splice.php
     *
     * @param int $offset
     * @param int|null $length
     * @param mixed $replacement
     * @param mixed|null $splicedResults
     * @return ArrayUtilities|Traverse
     */
    public function splice(
        int $offset, ?int $length, mixed $replacement = [], mixed &$splicedResults = null
    ): self {
        $splicedResults = array_splice($this->raw, $offset, $length, $replacement);
        $splicedResults = new self($splicedResults);
        return $this;
    }

    /**
     * Extract a slice of the array
     * https://www.php.net/manual/en/function.array-slice.php
     *
     * @param int $offset
     * @param int|null $length
     * @param bool $preserveKeys
     * @return ArrayUtilities|Traverse
     */
    public function slice(int $offset, ?int $length, bool $preserveKeys = false): self {
        $this->raw = array_slice($this->raw, $offset, $length, $preserveKeys);
        return $this;
    }

    /**
     * Computes the difference of arrays
     * https://www.php.net/manual/en/function.array-diff.php
     *
     * @param array|TraverseInterface $array
     * @return Traverse|ArrayUtilities
     */
    public function diff(array|TraverseInterface $array): self
    {
        $array = $this->handleCollectArg($array);
        $this->raw = array_diff($this->raw, $array);
        return $this;
    }

    /**
     * Computes the difference of arrays with additional index check
     * https://www.php.net/manual/en/function.array-diff-assoc.php
     *
     * @param array|TraverseInterface $array
     * @return Traverse|ArrayUtilities
     */
    public function diffAssoc(array|TraverseInterface $array): self
    {
        $array = $this->handleCollectArg($array);
        $this->raw = array_diff_assoc($this->raw, $array);
        return $this;
    }

    /**
     * Computes the difference of arrays using keys for comparison
     * https://www.php.net/manual/en/function.array-diff-key.php
     *
     * @param array|TraverseInterface $array
     * @return Traverse|ArrayUtilities
     */
    public function diffKey(array|TraverseInterface $array): self
    {
        $array = $this->handleCollectArg($array);
        $this->raw = array_diff_key($this->raw, $array);
        return $this;
    }

    /**
     * Removes duplicate values from an array
     * https://www.php.net/manual/en/function.array-unique.php
     *
     * @param int $flags
     * @return Traverse|ArrayUtilities
     */
    public function unique(int $flags = SORT_STRING): self
    {
        $this->raw = array_unique($this->raw, $flags);
        return $this;
    }


    /**
     * Will only return duplicate items
     *
     * @return Traverse|ArrayUtilities
     */
    public function duplicates(): self
    {
        $this->raw = array_unique(array_diff_assoc($this->raw, array_unique($this->raw)));
        return $this;
    }

    /**
     * Exchanges all keys with their associated values in an array
     * https://www.php.net/manual/en/function.array-flip.php
     *
     * @return Traverse|ArrayUtilities
     */
    public function flip(): self
    {
        $this->raw = array_flip($this->raw);
        return $this;
    }

    /**
     * Unset a given variable
     * https://www.php.net/manual/en/function.unset.php
     *
     * @param string ...$keySpread
     * @return Traverse|ArrayUtilities
     */
    public function unset(string|array ...$keySpread): self
    {
        $inst = new self($keySpread);
        $flatten = $inst->flatten()->toArray();
        foreach ($flatten as $key) {
            unset($this->raw[$key]);
        }
        return $this;
    }

    /**
     * Return the values from a single column in the input array
     * https://www.php.net/manual/en/function.array-column.php
     *
     * @param int|string|null $columnKey
     * @param int|string|null $indexKey
     * @return Traverse|ArrayUtilities
     */
    public function column(int|string|null $columnKey, int|string|null $indexKey = null): self
    {
        $this->raw = array_column($this->raw, $columnKey, $indexKey);
        return $this;
    }

    /**
     * ALIAS TO column
    */
    public function pluck(int|string|null $columnKey, int|string|null $indexKey = null): self
    {
        return $this->column($columnKey, $indexKey);
    }

    /**
     * Shift an element off the beginning of array
     * https://www.php.net/manual/en/function.array-shift.php
     *
     * @param mixed $value
     * @return Traverse|ArrayUtilities
     */
    public function shift(mixed &$value = null): self
    {
        $value = array_shift($this->raw);
        return $this;
    }

    /**
     * Pop the element off the end of array
     * https://www.php.net/manual/en/function.array-pop.php
     *
     * @param mixed $value
     * @return Traverse|ArrayUtilities
     */
    public function pop(mixed &$value = null): self
    {
        $value = array_pop($this->raw);
        return $this;
    }

    /**
     * Prepend one or more elements to the beginning of an array
     * https://www.php.net/manual/en/function.array-unshift.php
     *
     * @param mixed $value
     * @return Traverse|ArrayUtilities
     */
    public function unshift(mixed ...$value): self
    {
        array_unshift($this->raw, ...$value);
        return $this;
    }

    /**
     * Push one or more elements onto the end of array
     * https://www.php.net/manual/en/function.array-push.php
     *
     * @param mixed $value
     * @return Traverse|ArrayUtilities
     */
    public function push(mixed ...$value): self
    {
        array_push($this->raw, ...$value);
        return $this;
    }

    /**
     * Pad array to the specified length with a value
     * https://www.php.net/manual/en/function.array-pad.php
     *
     * @param int $length
     * @param mixed $value
     * @return Traverse|ArrayUtilities
     */
    public function pad(int $length, mixed $value): self
    {
        $this->raw = array_pad($this->raw, $length, $value);
        return $this;
    }

    /**
     * Fill an array with values
     * https://www.php.net/manual/en/function.array-fill.php
     *
     * @param int $startIndex
     * @param int $count
     * @param mixed $value
     * @return Traverse|ArrayUtilities
     */
    public function fill(int $startIndex, int $count, mixed $value): self
    {
        $this->raw = array_fill($startIndex, $count, $value);
        return $this;
    }

    /**
     * Create an array containing a range of elements
     * https://www.php.net/manual/en/function.range.php
     *
     * @param string|int|float $start
     * @param string|int|float $end
     * @param int|float $step
     * @return Traverse|ArrayUtilities
     */
    public function range(string|int|float $start, string|int|float $end, int|float $step = 1): self
    {
        $this->raw = range($start, $end, $step);
        return $this;
    }

    /**
     * Shuffle an array
     * https://www.php.net/manual/en/function.shuffle.php
     *
     * @return Traverse|ArrayUtilities
     */
    public function shuffle(): self
    {
        shuffle($this->raw);
        return $this;
    }

    /**
     * Pick one or more random keys out of an array
     * https://www.php.net/manual/en/function.array-rand.php
     *
     * @param int $num
     * @return Traverse|ArrayUtilities
     */
    public function rand(int $num = 1): self
    {
        $this->raw = array_rand($this->raw, $num);
        return $this;
    }

    /**
     * Replaces elements from passed arrays into the first array
     * https://www.php.net/manual/en/function.array-replace.php
     *
     * @param array ...$replacements
     * @return ArrayUtilities|Traverse
     */
    public function replace(array ...$replacements): self
    {
        $this->raw = array_replace($this->raw, ...$replacements);
        return $this;
    }

    /**
     * Replaces elements from passed arrays into the first array
     * https://www.php.net/manual/en/function.array-replace.php
     *
     * @param array ...$replacements
     * @return ArrayUtilities|Traverse
     */
    public function replaceRecursive(array ...$replacements): self
    {
        $this->raw = array_replace_recursive($this->raw, ...$replacements);
        return $this;
    }

    /**
     * Return an array with elements in reverse order
     * https://www.php.net/manual/en/function.array-reverse.php
     *
     * @param bool $preserveKeys
     * @return ArrayUtilities|Traverse
     */
    public function reverse(bool $preserveKeys = false): self
    {
        $this->raw = array_reverse($this->raw, $preserveKeys);
        return $this;
    }

    /**
     * Searches and filter out the array items that is found
     * https://www.php.net/manual/en/function.array-search.php
     *
     * @param mixed $needle
     * @param bool $strict
     * @return ArrayUtilities|Traverse
     */
    public function searchFilter(array $needle, bool $strict = false): self
    {
        return $this->filter(function ($item) use($needle, $strict) {
            return !in_array($item, $needle, $strict);
        });
    }

    /**
     * Searches and filter out the array items that is not found
     * https://www.php.net/manual/en/function.array-search.php
     *
     * @param mixed $needle
     * @param bool $strict
     * @return ArrayUtilities|Traverse
     */
    public function searchMatch(array $needle, bool $strict = false): self
    {
        return $this->filter(function ($item) use($needle, $strict) {
            return in_array($item, $needle, $strict);
        });
    }

    /**
     * Apply a user supplied function to every member of an array
     * https://www.php.net/manual/en/function.array-walk.php
     *
     * @param array $needle
     * @param bool $strict
     * @return Traverse|ArrayUtilities
     */
    public function select(array $needle, bool $strict = false): self
    {
        return $this->filter(function ($keyItem) use($needle, $strict) {
            return in_array($keyItem, $needle, $strict);
        }, ARRAY_FILTER_USE_KEY);
    }

    /**
     * Apply a user supplied function to every member of an array
     * https://www.php.net/manual/en/function.array-walk.php
     *
     * @param Closure $call
     * @param mixed|null $arg
     * @return Traverse|ArrayUtilities
     */
    public function walk(Closure $call, mixed $arg = null): self
    {
        $this->raw = $this->toArray();
        $call = Closure::bind($call, $this);
        array_walk($this->raw, $call, $arg);
        return $this;
    }

    /**
     * Apply a user function recursively to every member of an array
     * https://www.php.net/manual/en/function.array-walk-recursive.php
     *
     * @param Closure $call
     * @param mixed|null $arg
     * @return Traverse|ArrayUtilities
     */
    public function walkRecursive(Closure $call, mixed $arg = null): self
    {
        $this->raw = $this->toArray();
        $call = Closure::bind($call, $this);
        array_walk_recursive($this->raw, $call, $arg);
        return $this;
    }

    /**
     * Get first item in collection
     * @return Traverse|ArrayUtilities
     */
    public function next(): self
    {
        next($this->raw);
        return $this;
    }

    /**
     * Get first item in collection
     * @return Traverse|ArrayUtilities
     */
    public function prev(): self
    {
        prev($this->raw);
        return $this;
    }

    /**
     * Fetch a key from an array
     * https://www.php.net/manual/en/function.key.php
     *
     * @return Traverse|ArrayUtilities
     */
    public function key(): self
    {
        $arr = $this->raw;
        if (!is_array($arr)) {
            $arr = $this->toArray();
        }
        $this->raw = key($arr);
        return $this;
    }

    /**
     * Return all the keys or a subset of the keys of an array
     * https://www.php.net/manual/en/function.array-keys.php
     *
     * @return Traverse|ArrayUtilities
     */
    public function keys(): self
    {
        $arr = $this->raw;
        if (!is_array($arr)) {
            $arr = $this->toArray();
        }
        $this->raw = array_keys($arr);
        return $this;
    }

    /**
     * A helper function to handle collect args
     *
     * @param array|TraverseInterface $collect
     * @return array
     */
    protected function handleCollectArg(array|TraverseInterface $collect): array
    {
        if ($collect instanceof TraverseInterface) {
            $collect = $collect->toArray();
        }
        return $collect;
    }
}