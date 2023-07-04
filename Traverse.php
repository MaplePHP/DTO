<?php 
/**
 * @Package: 	PHPFuse - The main traverse class
 * @Author: 	Daniel Ronkainen
 * @Licence: 	The MIT License (MIT), Copyright © Daniel Ronkainen
 				Don't delete this comment, its part of the license.
 */

namespace PHPFuse\DTO;

use PHPFuse\DTO\Format;

class Traverse extends dynamicDataAbstract {

	protected $row; // Use row to access current instance (access inst/object)
	protected $raw; // Use raw to access current instance data (access array)
	
	/**
	 * Init intance
	 * @param  array|object $data [description]
	 * @return static
	 */
	static function value($data, $raw = NULL) {
		$inst = new static();
		$inst->raw = $raw;

		if(is_array($data) || is_object($data)) {
			foreach($data as $k => $v) $inst->{$k} = $v;
		}
		return $inst;
	}

	/**
	 * Get/return result
	 * @return inherit
	 */
	function get(?string $fallback = NULL) {
		return (!is_null($this->row) ? $this->row : $fallback);
	}
	
	/**
	 * Traverse factory 
	 * If you want 
	 * @return self
	 */
	function __call($a, $b) {
		
		$this->row = ($this->{$a} ?? NULL);
		$this->raw = $this->row;
		
		if(count($b) > 0) {
			$name = ucfirst($b[0]);
			$r = new \ReflectionClass("PHPFuse\\DTO\\Format\\{$name}");
			$instance = $r->newInstanceWithoutConstructor();
			return $instance->value($this->row);
		}

		if(is_array($this->row) || is_object($this->row)) {
			return $this::value($this->row, $this->raw);
		}
		
		return $this;
	}


	function getRaw() {
		return $this->raw;
	}

	/**
	 * Access incremental array
	 * @param  string   $key      Column name
	 * @param  callable $callback Access array row in the callbacks argumnet 1
	 * @return self
	 */
	function fetch(?callable $callback = NULL) {
		$new = array();
		foreach($this->raw as $key => $row) {
			if(!is_null($callback)) {
				$new[$key] = $callback($r, $key, $row);

			} else {

				if(is_array($row) || (is_object($row) && ($row instanceOf \stdClass))) {
					// Incremental -> object
					$r = $this::value($row);

				} else if(is_object($row)) {
					$r = $row;
					
				} else {
					// Incremental -> value
					$r = Format\Str::value($row);
				}
			
				$new[$key] = $r;
			}
		}

		$this->row = $new;
		return $this;
	}

	/**
	 * Count if row is array. Can be used to validate before @fetch method
	 * @return int
	 */
	function count() {
		return (is_array($this->raw) ? count($this->raw) : 0);
	}

	function isset(): mixed 
	{
		return (isset($this->raw)) ? $this->row : false;
	}

	/**
	 * Create a fallback value if value is Empty/Null/0/false
	 * @param  string $fallback
	 * @return mixed
	 */
	function fallback(mixed $fallback) {
		if(!$this->row) $this->row = $fallback;
		return $this;
	}

	function sprint(string $add) {
		if(!is_null($this->row)) $this->row = sprintf($add, $this->row);
		return $this;
	}

	

}
