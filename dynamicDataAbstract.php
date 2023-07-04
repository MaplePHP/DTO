<?php
/**
 * @Package: 	PHPFuse Dynamic data abstraction Class
 * @Author: 	Daniel Ronkainen
 * @Licence: 	The MIT License (MIT), Copyright © Daniel Ronkainen
 				Don't delete this comment, its part of the license.
 */

namespace PHPFuse\DTO;

abstract class dynamicDataAbstract {

	private $data;

    abstract function get();

	public function __construct() 
    {
        $this->data = new \stdClass();
    }

    public function __toString() 
    {
        return $this->get();
    }

    public function __set($key, $value)
    {
        $this->data->{$key} = $value;
    }

    public function __get($key)
    {
        return ($this->data->{$key} ?? NULL);
    }

    public function getData(): mixed 
    {
    	return $this->data;
    }    

}
