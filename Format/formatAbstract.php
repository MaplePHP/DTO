<?php
/**
 * @Package:    PHPFuse Dynamic data abstraction Class
 * @Author:     Daniel Ronkainen
 * @Licence:    The MIT License (MIT), Copyright © Daniel Ronkainen
                Don't delete this comment, its part of the license.
 */

namespace PHPFuse\DTO\Format;

abstract class formatAbstract {

    protected $value;

    /**
     * Init format by adding data to modify/format/traverse
     * @param  array  $arr
     * @return self
     */
    public static function value($value): FormatInterface 
    {
        $inst = new static();
        $inst->value = $value;
        return $inst;
    }
    
    /**
     * Get DTO value
     * @return mixed
     */
    public function get(): mixed 
    {
        return $this->value;
    }

    /**
     * Set a fallback value if current value is empty
     * @param  string $fallback
     * @return self
     */
    public function fallback(string $fallback): self 
    {
        if(!$this->value) $this->value = $fallback;
        return $this;
    }

    /**
     * Clone data
     * @return static
     */
    public function clone(): self 
    {
        return clone $this;
    }

    /**
     * Get Value
     * @return string
     */
    public function __toString() 
    {
        return $this->get();
    }


    /**
     * Sprit
     * @param  string $add
     * @return self
     */
    function sprint(string $add) {
        if(!is_null($this->value)) $this->value = sprintf($add, $this->value);
        return $this;
    }

}
