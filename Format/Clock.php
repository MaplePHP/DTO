<?php

/**
 * @Package:    MaplePHP Format string
 * @Author:     Daniel Ronkainen
 * @Licence:    Apache-2.0 license, Copyright © Daniel Ronkainen
                Don't delete this comment, its part of the license.
 */

namespace MaplePHP\DTO\Format;

use Exception;
use InvalidArgumentException;

final class Clock extends FormatAbstract implements FormatInterface
{
    /**
     * Input is mixed data type in the interface because I do not know the type before
     * The class constructor MUST handle the input validation
     * @param string $value
     * @throws Exception
     */
    public function __construct(mixed $value)
    {
        if (is_array($value) || is_object($value)) {
            throw new InvalidArgumentException("Is expecting a string or a convertable string value.", 1);
        }

        parent::__construct(new \DateTime($value));
    }

    /**
     * Format date data
     * @param string $format
     * @return object
     */
    public function format(string $format = 'Y-m-d H:i:s'): string
    {
        return $this->value->format($format);
    }

    /**
     * Get DTO value
     * @return mixed
     */
    public function get(): mixed
    {
        return $this->value->format('Y-m-d H:i:s');
    }

    /**
     * Init format by adding data to modify/format/traverse
     * @param mixed $value
     * @return self
     * @throws Exception
     */
    public static function value(mixed $value): FormatInterface
    {
        return new Clock((string)$value);
    }

    /**
     * Get Value
     * @return string
     */
    public function __toString(): string
    {
        return (string)$this->get();
    }
}
