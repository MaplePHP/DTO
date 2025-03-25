<?php

/**
 * @Package:    MaplePHP Format array
 * @Author:     Daniel Ronkainen
 * @Licence:    Apache-2.0 license, Copyright © Daniel Ronkainen
                Don't delete this comment, its part of the license.
 * @Version:    1.0.0
 */

namespace MaplePHP\DTO\Format;

use MaplePHP\DTO\Traits\CollectionUtilities;
use MaplePHP\DTO\Traverse;

final class Arr extends FormatAbstract implements FormatInterface
{
    use CollectionUtilities;

    /**
     * This will make the collection accessible through static initiations
     * Input is mixed data type in the interface because I do not know the type before
     * The class constructor MUST handle the input validation
     *
     * @param string $value
     */
    public function __construct(mixed $value)
    {
        if (!is_array($value)) {
            throw new \InvalidArgumentException("Is expecting a string or a convertable string value.", 1);
        }
        parent::__construct($value);
    }

    /**
     * This will access parts of collection that are otherwise not reachable
     *
     * @param string $name
     * @param array $arguments
     * @return mixed
     */
    function __call(string $name, array $arguments)
    {
        $inst = new Traverse($this->raw);
        if(!method_exists($inst, $name)) {
            throw new \BadMethodCallException("Method '$name' does not exist.");
        }
        return $inst->$name(...$arguments);
    }

    /**
     * Init format by adding data to modify/format/traverse
     * '
     * @param  mixed $value
     * @return self
     */
    public static function value(mixed $value): FormatInterface
    {
        $inst = new static($value);
        return $inst;
    }

    /**
     * Get array keys
     *
     * @return self
     */
    public function arrayKeys(): self
    {
        return $this->keys();
    }
}
