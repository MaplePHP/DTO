<?php

namespace MaplePHP\DTO\Traits;

use Closure;
use MaplePHP\DTO\Format\Arr;
use MaplePHP\DTO\Helpers;
use MaplePHP\DTO\Traverse;
use MaplePHP\DTO\TraverseInterface;

trait CollectionUtilities
{
    /**
     * Applies the callback to the elements of the given arrays
     * https://www.php.net/manual/en/function.array-map.php
     *
     * @param callable $callback A callable to run for each element in each array.
     * @param array $array Supplementary variable list of array arguments
     * @return Arr|CollectionUtilities|Traverse
     */
    public function map(callable $callback, array ...$array): self
    {
        $inst = clone $this;
        $inst->raw = array_map($callback, $inst->fetch(), ...$array);
        return $inst;
    }

    /**
     * Filters elements of an array using a callback function
     * https://www.php.net/manual/en/function.array-filter.php
     *
     * @param callable|null $callback The callback function to use
     *                                   If no callback is supplied, all empty entries of array will be
     *                                   removed. See empty() for how PHP defines empty in this case.
     * @param int $mode Flag determining what arguments are sent to callback:
     * @return Arr|CollectionUtilities|Traverse
     */
    public function filter(?callable $callback = null, int $mode = 0): self
    {
        $inst = clone $this;
        $data = is_null($callback) ? $inst->raw : $inst->fetch();
        $inst->raw = array_filter($data, $callback, $mode);
        return $inst;
    }

    /**
     * Iteratively reduce the array to a single value using a callback function
     * https://www.php.net/manual/en/function.array-reduce.php
     *
     * @param callable $callback
     * @param mixed|null $initial
     * @return Arr|CollectionUtilities|Traverse
     */
    public function reduce(callable $callback, mixed $initial = null): self
    {
        $inst = clone $this;
        $inst->raw = array_reduce($inst->fetch(), $callback, $initial);
        return $inst;
    }

    /**
     * Split an array into chunks
     * https://www.php.net/manual/en/function.array-chunk.php
     *
     * @param int $length
     * @param bool $preserveKeys
     * @return Arr|CollectionUtilities|Traverse
     */
    public function chunk(int $length, bool $preserveKeys = false): self
    {
        $inst = clone $this;
        $inst->raw = array_chunk($inst->fetch(), $length, $preserveKeys);
        return $inst;
    }

    /**
     * Flatten a array
     *
     * @param bool $preserveKeys
     * @return Arr|CollectionUtilities|Traverse
     */
    public function flatten(bool $preserveKeys = false): self
    {
        $inst = clone $this;
        $result = [];
        $array = $inst->toArray();
        array_walk_recursive($array, function ($item, $key) use (&$result, $inst, $preserveKeys) {
            $item = $inst->with($item);
            if ($preserveKeys) {
                $result[$key] = $item;
            } else {
                $result[] = $item;
            }
        });
        $inst->raw = $result;
        return $inst;
    }

    /**
     * Flatten array and preserve keys
     *
     * @return Arr|CollectionUtilities|Traverse
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
     * @return Arr|CollectionUtilities|Traverse
     */
    public function merge(array|TraverseInterface $combine, bool $before = false): self
    {
        $inst = clone $this;
        $combine = $inst->handleCollectArg($combine);
        if ($before) {
            $inst->raw = array_merge($combine, $inst->raw);
        } else {
            $inst->raw = array_merge($inst->raw, $combine);
        }
        return $inst;
    }

    /**
     * Append array after main collection
     *
     * @param array|TraverseInterface $combine
     * @return Arr|CollectionUtilities|Traverse
     */
    public function append(array|TraverseInterface $combine): self
    {
        return $this->merge($combine);
    }

    /**
     * Perpend array after main collection
     *
     * @param array|TraverseInterface $combine
     * @return Arr|CollectionUtilities|Traverse
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
     * @return Arr|CollectionUtilities|Traverse
     */
    public function splice(
        int $offset, ?int $length, mixed $replacement = [], mixed &$splicedResults = null
    ): self {
        $inst = clone $this;
        $splicedResults = array_splice($inst->raw, $offset, $length, $replacement);
        $splicedResults = new self($splicedResults);
        return $inst;
    }

    /**
     * Extract a slice of the array
     * https://www.php.net/manual/en/function.array-slice.php
     *
     * @param int $offset
     * @param int|null $length
     * @param bool $preserveKeys
     * @return Arr|CollectionUtilities|Traverse
     */
    public function slice(int $offset, ?int $length, bool $preserveKeys = false): self
    {
        $inst = clone $this;
        $inst->raw = array_slice($inst->raw, $offset, $length, $preserveKeys);
        return $inst;
    }

    /**
     * Computes the difference of arrays
     * https://www.php.net/manual/en/function.array-diff.php
     *
     * @param array|TraverseInterface $array
     * @return Arr|CollectionUtilities|Traverse
     */
    public function diff(array|TraverseInterface $array): self
    {
        $inst = clone $this;
        $array = $inst->handleCollectArg($array);
        $inst->raw = array_diff($inst->raw, $array);
        return $inst;
    }

    /**
     * Computes the difference of arrays with additional index check
     * https://www.php.net/manual/en/function.array-diff-assoc.php
     *
     * @param array|TraverseInterface $array
     * @return Arr|CollectionUtilities|Traverse
     */
    public function diffAssoc(array|TraverseInterface $array): self
    {
        $inst = clone $this;
        $array = $inst->handleCollectArg($array);
        $inst->raw = array_diff_assoc($inst->raw, $array);
        return $inst;
    }

    /**
     * Computes the difference of arrays using keys for comparison
     * https://www.php.net/manual/en/function.array-diff-key.php
     *
     * @param array|TraverseInterface $array
     * @return Arr|CollectionUtilities|Traverse
     */
    public function diffKey(array|TraverseInterface $array): self
    {
        $inst = clone $this;
        $array = $inst->handleCollectArg($array);
        $inst->raw = array_diff_key($inst->raw, $array);
        return $inst;
    }

    /**
     * Removes duplicate values from an array
     * https://www.php.net/manual/en/function.array-unique.php
     *
     * @param int $flags
     * @return Arr|CollectionUtilities|Traverse
     */
    public function unique(int $flags = SORT_STRING): self
    {
        $inst = clone $this;
        $inst->raw = array_unique($inst->raw, $flags);
        return $inst;
    }


    /**
     * Will only return duplicate items
     *
     * @return Arr|CollectionUtilities|Traverse
     */
    public function duplicates(): self
    {
        $inst = clone $this;
        $inst->raw = array_unique(array_diff_assoc($inst->raw, array_unique($inst->raw)));
        return $inst;
    }

    /**
     * Exchanges all keys with their associated values in an array
     * https://www.php.net/manual/en/function.array-flip.php
     *
     * @return Arr|CollectionUtilities|Traverse
     */
    public function flip(): self
    {
        $inst = clone $this;
        $inst->raw = array_flip($inst->raw);
        return $inst;
    }

    /**
     * Unset a given variable
     * https://www.php.net/manual/en/function.unset.php
     *
     * @param string ...$keySpread
     * @return Arr|CollectionUtilities|Traverse
     */
    public function unset(string|int|float|array ...$keySpread): self
    {
        $inst = clone $this;
        $newInst = new self($keySpread);
        $flatten = $newInst->flatten()->toArray();
        foreach ($flatten as $key) {
            unset($inst->raw[$key]);
        }
        return $inst;
    }

    /**
     * Will explode an array item value and then merge it into array in same hierarchy
     *
     * @param string $separator
     * @return Arr|CollectionUtilities|Traverse
     */
    public function expMerge(string $separator): self
    {
        $new = [];
        foreach ($this->raw as $item) {
            $exp = explode($separator, (string)$item);
            $new = array_merge($new, $exp);
        }
        $this->raw = $new;
        return $this;
    }

    // Alias to expMerge
    public function arrayItemExpMerge(string $separator): self
    {
        return $this->expMerge($separator);
    }

    /**
     * Return the values from a single column in the input array
     * https://www.php.net/manual/en/function.array-column.php
     *
     * @param int|string|null $columnKey
     * @param int|string|null $indexKey
     * @return Arr|CollectionUtilities|Traverse
     */
    public function column(int|string|null $columnKey, int|string|null $indexKey = null): self
    {
        $inst = clone $this;
        $inst->raw = array_column($inst->raw, $columnKey, $indexKey);
        return $inst;
    }

    /**
     * Alias to column
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
     * @return Arr|CollectionUtilities|Traverse
     */
    public function shift(mixed &$value = null): self
    {
        $inst = clone $this;
        $value = array_shift($inst->raw);
        return $inst;
    }

    /**
     * Pop the element off the end of array
     * https://www.php.net/manual/en/function.array-pop.php
     *
     * @param mixed $value
     * @return Arr|CollectionUtilities|Traverse
     */
    public function pop(mixed &$value = null): self
    {
        $inst = clone $this;
        $value = array_pop($inst->raw);
        return $inst;
    }

    /**
     * Prepend one or more elements to the beginning of an array
     * https://www.php.net/manual/en/function.array-unshift.php
     *
     * @param mixed $value
     * @return Arr|CollectionUtilities|Traverse
     */
    public function unshift(mixed ...$value): self
    {
        $inst = clone $this;
        array_unshift($inst->raw, ...$value);
        return $inst;
    }

    /**
     * Push one or more elements onto the end of array
     * https://www.php.net/manual/en/function.array-push.php
     *
     * @param mixed $value
     * @return Arr|CollectionUtilities|Traverse
     */
    public function push(mixed ...$value): self
    {
        $inst = clone $this;
        array_push($inst->raw, ...$value);
        return $inst;
    }

    /**
     * Pad array to the specified length with a value
     * https://www.php.net/manual/en/function.array-pad.php
     *
     * @param int $length
     * @param mixed $value
     * @return Arr|CollectionUtilities|Traverse
     */
    public function pad(int $length, mixed $value): self
    {
        $inst = clone $this;
        $inst->raw = array_pad($inst->raw, $length, $value);
        return $inst;
    }

    /**
     * Fill an array with values
     * https://www.php.net/manual/en/function.array-fill.php
     *
     * @param int $startIndex
     * @param int $count
     * @param mixed $value
     * @return Arr|CollectionUtilities|Traverse
     */
    public function fill(int $startIndex, int $count, mixed $value): self
    {
        $inst = clone $this;
        $inst->raw = array_fill($startIndex, $count, $value);
        return $inst;
    }

    /**
     * Create an array containing a range of elements
     * https://www.php.net/manual/en/function.range.php
     *
     * @param string|int|float $start
     * @param string|int|float $end
     * @param int|float $step
     * @return Arr|CollectionUtilities|Traverse
     */
    public function range(string|int|float $start, string|int|float $end, int|float $step = 1): self
    {
        $inst = clone $this;
        $inst->raw = range($start, $end, $step);
        return $inst;
    }

    /**
     * Shuffle an array
     * https://www.php.net/manual/en/function.shuffle.php
     *
     * @return Arr|CollectionUtilities|Traverse
     */
    public function shuffle(): self
    {
        $inst = clone $this;
        shuffle($inst->raw);
        return $inst;
    }

    /**
     * Pick one or more random keys out of an array
     * https://www.php.net/manual/en/function.array-rand.php
     *
     * @param int $num
     * @return Arr|CollectionUtilities|Traverse
     */
    public function rand(int $num = 1): self
    {
        $inst = clone $this;
        $inst->raw = array_rand($inst->raw, $num);
        return $inst;
    }

    /**
     * Replaces elements from passed arrays into the first array
     * https://www.php.net/manual/en/function.array-replace.php
     *
     * @param array ...$replacements
     * @return Arr|CollectionUtilities|Traverse
     */
    public function replace(array ...$replacements): self
    {
        $inst = clone $this;
        $inst->raw = array_replace($inst->raw, ...$replacements);
        return $inst;
    }

    /**
     * Replaces elements from passed arrays into the first array
     * https://www.php.net/manual/en/function.array-replace.php
     *
     * @param array ...$replacements
     * @return Arr|CollectionUtilities|Traverse
     */
    public function replaceRecursive(array ...$replacements): self
    {
        $inst = clone $this;
        $inst->raw = array_replace_recursive($inst->raw, ...$replacements);
        return $inst;
    }

    /**
     * Return an array with elements in reverse order
     * https://www.php.net/manual/en/function.array-reverse.php
     *
     * @param bool $preserveKeys
     * @return Arr|CollectionUtilities|Traverse
     */
    public function reverse(bool $preserveKeys = false): self
    {
        $inst = clone $this;
        $inst->raw = array_reverse($inst->raw, $preserveKeys);
        return $inst;
    }

    /**
     * Searches the array for a given value and returns the first corresponding 'key' if
     * successful to collection
     * https://www.php.net/manual/en/function.array-search.php
     *
     * @param mixed $needle
     * @param bool $strict
     * @return Arr|CollectionUtilities|Traverse
     */
    public function search(mixed $needle, bool $strict = false): self
    {
        $inst = clone $this;
        $inst->raw = array_search($needle, $this->raw, $strict);
        return $inst;
    }

    /**
     * Searches the array for a given value and returns the first corresponding 'value' if
     * successful to collection
     *
     * @param mixed $needle
     * @param bool $strict
     * @return Arr|CollectionUtilities|Traverse
     */
    public function find(mixed $needle, bool $strict = false): self
    {
        $inst = clone $this;
        $key = $this->search($needle, $strict)->get();
        if($key === false) {
            $inst->raw = null;
            return $inst;
        }
        return $inst->eq($key);
    }

    /**
     * Searches and filter out the array items that is found
     * https://www.php.net/manual/en/function.array-search.php
     *
     * @param mixed $needle
     * @param bool $strict
     * @return Arr|CollectionUtilities|Traverse
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
     * @return Arr|CollectionUtilities|Traverse
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
     * @return Arr|CollectionUtilities|Traverse
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
     * @return Arr|CollectionUtilities|Traverse
     */
    public function walk(Closure $call, mixed $arg = null): self
    {
        $inst = clone $this;
        $inst->raw = $inst->toArray();
        $call = Closure::bind($call, $inst);
        array_walk($inst->raw, $call, $arg);
        return $inst;
    }

    /**
     * Apply a user function recursively to every member of an array
     * https://www.php.net/manual/en/function.array-walk-recursive.php
     *
     * @param Closure $call
     * @param mixed|null $arg
     * @return Arr|CollectionUtilities|Traverse
     */
    public function walkRecursive(Closure $call, mixed $arg = null): self
    {
        $inst = clone $this;
        $inst->raw = $inst->toArray();
        $call = Closure::bind($call, $inst);
        array_walk_recursive($inst->raw, $call, $arg);
        return $inst;
    }

    /**
     * Get first item in collection
     * @return Arr|CollectionUtilities|Traverse
     */
    public function next(): self
    {
        $inst = clone $this;
        next($inst->raw);
        return $inst;
    }

    /**
     * Get first item in collection
     * @return Arr|CollectionUtilities|Traverse
     */
    public function prev(): self
    {
        $inst = clone $this;
        prev($inst->raw);
        return $inst;
    }

    /**
     * Get first item in collection
     * https://www.php.net/manual/en/function.reset.php
     *
     * @return Arr|CollectionUtilities|Traverse
     */
    public function reset(): self
    {
        $inst = clone $this;
        $inst->raw = reset($this->raw);
        return $inst;
    }

    // Alias to 'reset'
    public function first(): self
    {
        return $this->reset();
    }

    /**
     * Get last item in collection
     * https://www.php.net/manual/en/function.end.php
     *
     * @return Arr|CollectionUtilities|Traverse
     */
    public function end(): self
    {
        $inst = clone $this;
        $inst->raw = end($this->raw);
        return $inst;
    }

    // Alias to 'end'
    public function last(): self
    {
        return $this->end();
    }

    /**
     * Fetch a key from an array
     * https://www.php.net/manual/en/function.key.php
     *
     * @return Arr|CollectionUtilities|Traverse
     */
    public function key(): self
    {
        $inst = clone $this;
        $arr = $inst->raw;
        if (!is_array($arr)) {
            $arr = $inst->toArray();
        }
        $inst->raw = key($arr);
        return $inst;
    }

    /**
     * Return all the keys or a subset of the keys of an array
     * https://www.php.net/manual/en/function.array-keys.php
     *
     * @return Arr|CollectionUtilities|Traverse
     */
    public function keys(): self
    {
        $inst = clone $this;
        $arr = $inst->raw;
        if (!is_array($arr)) {
            $arr = $inst->toArray();
        }
        $inst->raw = array_keys($arr);
        return $inst;
    }

    /**
     * Join array elements with a string
     * https://www.php.net/implode
     *
     * @param array|string $separator
     * @return Arr|CollectionUtilities|Traverse
     */
    public function implode(array|string $separator = ""): self
    {
        $inst = clone $this;
        $inst->raw = implode($separator, $inst->raw);
        return $inst;
    }

    /**
     * Will return array item at index/key
     * https://www.php.net/manual/en/function.shuffle.php
     *
     * @param int|float|string $key
     * @return Arr|CollectionUtilities|Traverse
     */
    public function eq(int|float|string $key): self
    {
        if (is_string($key) && str_contains($key, ".")) {
            return new self(Helpers::traversArrFromStr($this->toArray(), $key));
        }
        return new self($this->raw[$key] ?? false);
    }

    /**
     * Extract all array items values in array with a wildcard search ("2025-*")
     *
     * @param string $search wildcard prefix
     * @param bool $searchByKey
     * @return Arr|CollectionUtilities|Traverse
     */
    public function wildcardMatch(string $search, bool $searchByKey = false): self
    {
        $inst = clone $this;
        $regex = "/^" . str_replace(['\*', '\?'], ['.*', '.'], preg_quote($search, '/')) . "$/";
        $matches = [];
        foreach ($inst->raw as $key => $element) {
            $find = $searchByKey ? $key : $element;
            if (preg_match($regex, $find)) {
                $matches[] = $element;
            }
        }
        $inst->raw = $matches;
        return $inst;
    }

    // Alias it wildcardMatch
    function wildcardSearch(string $search, bool $searchByKey = false): self
    {
        return $this->wildcardMatch($search, $searchByKey);
    }

    /**
     * Find all array keys array with a wildcard search ("prefix_*")
     *
     * @param string $search wildcard prefix
     * @return Arr|CollectionUtilities|Traverse
     */
    public function wildcardMatchKeys(string $search): self
    {
        return $this->wildcardSearch($search, true);
    }

    /**
     * Create a fallback value if value is Empty/Null/0/false
     *
     * @param string $fallback
     * @return Arr|CollectionUtilities|Traverse
     */
    public function fallback(mixed $fallback): self
    {
        $inst = clone $this;
        if (!$this->isset()) {
            $inst->raw = $fallback;
        }
        return $inst;
    }

    /**
     * Sprint over values
     *
     * @param string $add
     * @return Arr|CollectionUtilities|Traverse
     */
    public function sprint(string $add): self
    {
        $inst = clone $this;
        if ($this->isset()) {
            $inst->raw = sprintf($add, $inst->raw);
        }
        return $inst;
    }

    /**
     * Same as value validate but will method chain.
     * If invalid then the value will be set to "null" OR whatever you set the fallback
     *
     * @param string $method
     * @param array $args
     * @param mixed|null $fallback
     * @return Arr|CollectionUtilities|Traverse
     * @throws \ErrorException
     */
    public function validOrFallback(string $method, array $args = [], mixed $fallback = null): self
    {
        $inst = clone $this;
        if(!$this->valid($method, $args)) {
            $inst->raw = $fallback;
        }
        return $inst;
    }

    /**
     * Calculate the sum of values in an array
     * https://www.php.net/manual/en/function.array-sum.php
     *
     * @return Arr|CollectionUtilities|Traverse
     */
    public function sum(): self
    {
        $inst = clone $this;
        $arr = $this->raw;
        if(!is_array($arr)) {
            $arr = $this->toArray();
        }
        $inst->raw = array_sum($arr);
        return $inst;
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