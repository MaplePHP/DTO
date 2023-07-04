<?php
/**
 * @Package: 	PHPFuse Format array
 * @Author: 	Daniel Ronkainen
 * @Licence: 	The MIT License (MIT), Copyright © Daniel Ronkainen
 				Don't delete this comment, its part of the license.
 * @Version: 	1.0.0
 */
namespace PHPFuse\DTO\Format;

class Local {

	static protected $prefix;
	protected $value;
	protected $sprint = array();

	/**
	 * Init format by adding data to modify/format/traverse
	 * @param  array  $arr
	 * @return self
	 */
	public static function value($arr) {
		$inst = new static();
		$inst->value = $arr;
		return $inst;
	}

	public static function setLang(string $prefix): void 
	{
		static::$prefix = $prefix;
	}

	public function lang(string $prefix): self 
	{
		$this::$prefix = $prefix;
		return $this;
	}

	public function sprint(array $sprint): self 
	{
		$this->sprint = $sprint;
		return $this;
	}

	public function getValue(string $key): ?string 
	{
		return ($this->value[$key][$this::$prefix] ?? NULL);
	}

	public function get(string|array $key, ?string $fallback = NULL, ?array $sprint = NULL): string 
	{
		if(is_null($this::$prefix)) throw new \Exception("Lang prefix is null.", 1);
		if(!is_null($sprint)) $this->sprint($sprint);

		if(is_array($key)) {
			$out = array();
			foreach($key as $k) $out[] = $this->getValue($k);
			return ucfirst(strtolower(implode(" ", $out)));
		}

		$value = ($this->value[$key][$this::$prefix] ?? $fallback);
		if(is_null($sprint)) return $value;

		return vsprintf(($this->value[$key][$this::$prefix] ?? $fallback), $this->sprint);
	}

	


}
