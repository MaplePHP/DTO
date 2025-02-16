<?php

/**
 * @Package:    MaplePHP Dynamic data abstraction Class
 * @Author:     Daniel Ronkainen
 * @Licence:    Apache-2.0 license, Copyright © Daniel Ronkainen
                Don't delete this comment, its part of the license.
 */

namespace MaplePHP\DTO;

abstract class DynamicDataAbstract
{
    private object $data;

    abstract public function get();

    public function __construct()
    {
        $this->data = new \stdClass();
    }

    public function __toString(): string
    {
        $val = $this->get();
        if (is_object($val)) {
            return serialize($val);
        }
        if (is_array($val)) {
            return json_encode($val);
        }
        if (is_resource($this->value)) {
            return get_resource_type($val);
        }
        return (string)$val;
    }

    public function __set($key, $value)
    {
        $this->data->{$key} = $value;
    }

    public function __get($key)
    {
        return ($this->data->{$key} ?? null);
    }

    public function getData(): mixed
    {
        return $this->data;
    }

    /**
     * Used to get a readable value
     * @return string
     * @throws ErrorException
     */
    public function toString(): string
    {
        return "(unknown type)";
    }
}
