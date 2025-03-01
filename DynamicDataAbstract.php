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

    /**
     * Will Always try to return a string from object
     *
     * @return string
     */
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

    /**
     * Will add item to object
     *
     * @param string $key  The object key name
     * @param mixed $value The object item value
     * @return void
     */
    public function __set(string $key, mixed $value): void
    {
        $this->addToObject($key, $value);
    }

    /**
     * Try to get data object item
     *
     * @param $key
     * @return null
     */
    public function __get($key)
    {
        return ($this->data->{$key} ?? null);
    }

    /**
     * Get the main data that will contain your encapsulated object
     *
     * @return mixed
     */
    public function getData(): mixed
    {
        return $this->data;
    }

    /**
     * Will add item to object but will not select it
     *
     * @param string $key  The object key name
     * @param mixed $value The object item value
     * @return self
     */
    public function addToObject(string $key, mixed $value): self
    {
        $this->data->{$key} = $value;
        return $this;
    }

    /**
     * Used to get a readable value
     * @return string
     */
    public function toString(): string
    {
        return "(unknown type)";
    }
}
