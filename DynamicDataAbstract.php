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
    protected $data = null;

    abstract public function get();

    public function __construct()
    {
        $this->data = new \stdClass();
    }

    public function __toString(): string
    {
        $val = $this->get();
        return (is_string($val) ? $val : "");
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
}
