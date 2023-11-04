<?php
/**
 * @Package:    PHPFuse Format array
 * @Author:     Daniel Ronkainen
 * @Licence:    The MIT License (MIT), Copyright © Daniel Ronkainen
                Don't delete this comment, its part of the license.
 * @Version:    1.0.0
 */

namespace PHPFuse\DTO\Format;

class Arr extends FormatAbstract implements FormatInterface
{
    /**
     * Unset array
     * @param  keys Keys that you want to unset (@unset("username", "password", "email", ....))
     * @return self
     */
    public function unset()
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
    public function arrayKeys()
    {
        $this->value = array_keys($this->value);
        return $this;
    }


    public function shift(?Str &$shiftedValue = null): Str
    {
        $inst = clone $this;
        $shift = array_shift($this->value);
        return $this;
    }

    public function pop(?Str &$poppedValue = null): Str
    {
        $inst = clone $this;
        $pop = array_pop($this->value);
        return $this;
    }

    /**
     * Extract all array items with arrat key prefix ("prefix_"name)
     * @param  string $search  wildcard prefix
     * @return self
     */
    public function wildcardSearch(string $search): self
    {
        $search = str_replace('\*', '.*?', preg_quote($search, '/'));
        $result = preg_grep('/^'.$search.'$/i', array_keys($this->value));
        $this->value = array_intersect_key($this->value, array_flip($result));
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
