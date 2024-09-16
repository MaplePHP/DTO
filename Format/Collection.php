<?php

/**
 * @Package:    MaplePHP Format string
 * @Author:     Daniel Ronkainen
 * @Licence:    Apache-2.0 license, Copyright © Daniel Ronkainen
Don't delete this comment, its part of the license.
 */

namespace MaplePHP\DTO\Format;

use InvalidArgumentException;

final class Collection extends FormatAbstract implements FormatInterface
{
    //protected $value;

    /**
     * Input is mixed data type in the interface because I do not know the type before
     * The class constructor MUST handle the input validation
     * @param string $value
     */
    public function __construct(mixed $value)
    {
        parent::__construct(new \ArrayObject($value));
    }

    public function __call($func, $argv)
    {
        if(method_exists($this->value, $func)) {
            $val = $this->value->{$func}(...$argv);
            $this->value = $this->value->getArrayCopy();

        } else {
            $copy = $this->value->getArrayCopy();
            $this->value = call_user_func_array($func, array_merge(array($copy), $argv));

        }

        return $this;
    }

    /**
     * Get/return result
     * @param mixed $value
     * @param bool $append
     * @return self
     */
    public function merge(mixed $value, bool $append = true): self
    {
        if($append) {
            $this->value = array_merge($this->value->getArrayCopy(), (array)$value);
        } else {
            $this->value = array_merge((array)$value, $this->value->getArrayCopy());
        }
        return $this;
    }

    /**
     * Get/return result
     * @param mixed $value
     * @return self
     */
    public function prepend(mixed $value): self
    {
        $value = [$value];
        $this->value = array_merge($value, $this->value->getArrayCopy());
        return $this;
    }
    /**
     * Init format by adding data to modify/format/traverse
     * @param  mixed $value
     * @return self
     */
    public static function value(mixed $value): FormatInterface
    {
        return new Collection($value);
    }

}
