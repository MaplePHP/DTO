<?php 
/**
 * @Package: 	PHPFuse Format date class with translations
 * @Author: 	Daniel Ronkainen
 * @Licence: 	The MIT License (MIT), Copyright © Daniel Ronkainen
 				Don't delete this comment, its part of the license.
 * @Version: 	1.0.0
 */
namespace PHPFuse\DTO\Format;

use DateTime as MainDateTime;
use DateTimeZone;

class DateTime extends MainDateTime implements FormatInterface {

	// Default lang key
	const DEFAULT_LANG = "sv";

	// Translation
	const LANG = [
		"sv" => [
			"Jan", "Feb", "Mar", "Apr", "Maj", "Jun", "Jul", "Aug", "Okt", "Sep", "Nov", "Dec", 
			"Januari", "Februari", "Mars", "April", "Maj", "Juni", "Juli", "Augusti", "Oktober", "September", "November", "December",
			"Måndag", "Tisdag", "Onsdag", "Torsdag", "Fredag", "Lördag", "Söndag"
		],
		"en" => [
			"Jan", "Feb", "Mar", "Apr", "May", "Jun", "Jul", "Aug", "Oct", "Sep", "Nov", "Dec", 
			"January", "February", "March", "April", "May", "June", "July", "August", "October", "September", "November", "December",
			"Monday", "Tuesday", "Wednesday", "Thursday", "Friday", "Saturday", "Sunday"
		]
	];

	private $lang;
	private $translations = array();
	

	function __construct(string $datetime = "now", ?DateTimeZone $timezone = null) {
		parent::__construct($datetime, $timezone);
		$this->translations = static::LANG;
	}

	/**
	 * Init
	 * @param  string $value
	 * @return new self
	 */
	static function value(string $value): \DateTime 
	{
		$inst = new self($value);
		return $inst;
	}

	/**
	 * Clone data
	 * @return static
	 */
	function clone() {
		return clone $this;
	}

	/**
	 * New instance
	 * @param  string $value
	 * @return new self
	 */
	function withValue(string $value) {
		return self::value($value);
	}

	/**
	 * Get Value
	 * @return string
	 */
	public function __toString() 
    {
        return $this->format("Y/m/d H:i");
    }

    /**
     * Gte formated date value
     * @param  string $format
     * @return string
     */
    function format(string $format): string 
	{
		$format = parent::format($format);
		return $this->translate($format);
	}

	/**
	 * Get all acceptable translation data
	 * @return array
	 */
	function getTranslations(): array 
	{
		return $this->translations;
	}
	
	/**
	 * Create translation
	 * @param string $key lang key
	 * @param array  $arr
	 * @return self
	 */
	function setTranslation(string $key, array $arr): self 
	{
		$this->translations[$key] = $arr;
		return $this;
	}

	/**
	 * Change expected language
	 * @param string $lang lang key
	 * @return self
	 */
	function setLanguage(string $lang): self 
	{
		if(!isset($this->translations[$lang])) {
			throw new \Exception("Translation for the language \"{$lang}\" does not exists! You can add custom translation with @setTranslation method.", 1);
		}
		$this->lang = $lang;
		return $this;
	}

	

	
	/**
	 * Get lang key
	 * @return string
	 */
	private function langKey(): string 
	{
		return (!is_null($this->lang)) ? $this->lang : $this::DEFAULT_LANG;
	}

	/**
	 * Get lang value
	 * @return string
	 */
	private function translate(string $format): string
	{
		$k = $this->langKey();		
		if(isset($this::LANG[$k])) {
			return str_replace($this::LANG['en'], $this::LANG[$k], $format);
		}
		return $format;
	}

}
