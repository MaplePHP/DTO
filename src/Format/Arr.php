<?php

/**
 * @Package:    MaplePHP Format array
 * @Author:     Daniel Ronkainen
 * @Licence:    Apache-2.0 license, Copyright © Daniel Ronkainen
                Don't delete this comment, its part of the license.
 * @Version:    1.0.0
 */

namespace MaplePHP\DTO\Format;

final class Arr extends FormatAbstract implements FormatInterface
{
    /**
     * Input is mixed data type in the interface because I do not know the type before
     * The class constructor MUST handle the input validation
     * @param string $value
     */
    public function __construct(mixed $value)
    {
        if (!is_array($value)) {
            throw new \InvalidArgumentException("Is expecting a string or a convertable string value.", 1);
        }
        parent::__construct((array)$value);
    }

    /**
     * Init format by adding data to modify/format/traverse
     * @param  mixed $value
     * @return self
     */
    public static function value(mixed $value): FormatInterface
    {
        $inst = new static($value);
        return $inst;
    }

    /**
     * Unset array
     * @return $this
     */
    public function unset(): self
    {
        $args = func_get_args();
        foreach ($args as $v) {
            unset($this->value[$v]);
        }
        return $this;
    }

    /**
     * Get array keys
     * @return self
     */
    public function arrayKeys(): self
    {
        $this->value = array_keys($this->value);
        return $this;
    }

    /**
     * Will explode an array item value and then merge it into array in same hierky
     * @param string $separator
     * @return self
     */
    public function arrayItemExpMerge(string $separator): self
    {
        $new = [];
        foreach ($this->value as $item) {
            $exp = explode($separator, $item);
            $new = array_merge($new, $exp);
        }
        $this->value = $new;
        return $this;
    }

    public function shift(?Str &$shiftedValue = null): self
    {
        //$inst = clone $this;
        $shiftedValue = array_shift($this->value);
        return $this;
    }

    public function pop(?Str &$poppedValue = null): self
    {
        //$inst = clone $this;
        $poppedValue = array_pop($this->value);
        return $this;
    }

    /**
     * Extract all array items with array key prefix ("prefix_"name)
     * @param  string $search  wildcard prefix
     * @return self
     */
    public function wildcardSearch(string $search): self
    {
        $regex = "/^" . str_replace(['\*', '\?'], ['.*', '.'], preg_quote($search, '/')) . "$/";
        $matches = [];
        foreach ($this->value as $element) {
            if (preg_match($regex, $element)) {
                $matches[] = $element;
            }
        }
        $this->value = $matches;
        return $this;
    }


    /**
     * Fill array
     * @param  int    $index
     * @param  int    $times
     * @param  string $value
     * @return self
     */
    public function fill(int $index, int $times, string $value = "&nbsp;"): self
    {
        $this->value = array_fill($index, $times, $value);
        return $this;
    }

    /**
     * Return count/length
     * @return int
     */
    public function count(): int
    {
        return count($this->value);
    }

    /**
     * Array walk over and make recursive changes to all array items
     * @param  callable $call return value with changes
     * @return self
     */
    public function walk(callable $call): self
    {
        $value = $this->value;
        array_walk_recursive($value, function (&$value) use ($call) {
            $value = $call($value);
        });
        $this->value = $value;
        return $this;
    }
}
