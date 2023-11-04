<?php
/**
 * @Package:    PHPFuse Dynamic data abstraction Class
 * @Author:     Daniel Ronkainen
 * @Licence:    The MIT License (MIT), Copyright © Daniel Ronkainen
                Don't delete this comment, its part of the license.
 */

namespace PHPFuse\DTO;

abstract class DynamicDataAbstract
{
    private $data;

    abstract public function get();

    public function __construct()
    {
        $this->data = new \stdClass();
    }

    public function __toString()
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
