<?php

/**
 * @Package:    MaplePHP Format array
 * @Author:     Daniel Ronkainen
 * @Licence:    The MIT License (MIT), Copyright © Daniel Ronkainen
                Don't delete this comment, its part of the license.
 * @Version:    1.0.0
 */

namespace MaplePHP\DTO\Format;

use Exception;

final class Local
{
    protected static $prefix;
    protected static $dir;
    protected static $data = array();

    protected $value;
    protected $sprint = array();

    /**
     * Init format by adding data to modify/format/traverse
     * @param  mixed  $data
     * @return self
     */
    public static function value(mixed $data)
    {

        if (is_string($data)) {
            if (is_null(self::$dir)) {
                throw new Exception("You need to set default lang directory.", 1);
            }
            if (!is_file(self::$dir . "{$data}.php")) {
                throw new Exception("Could not find the language file ({$data}) in \"" . self::$dir . "\".", 1);
            }

            if (!isset(self::$data[$data])) {
                self::$data[$data] = require_once(self::$dir . "{$data}.php");
                if (!is_array(self::$data[$data])) {
                    throw new Exception("The language file ({$data}) needs to be returned as an array!", 1);
                }
            }

            if (is_null(self::$data[$data])) {
                throw new Exception("Could not propagate the language data object with any information.", 1);
            }

            $data = self::$data[$data];
        }

        $inst = new static();
        $inst->value = $data;
        return $inst;
    }

    /**
     * Set directory
     * @param string $dir
     */
    public static function setDir(string $dir): void
    {
        static::$dir = $dir;
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
        return ($this->value[$key][$this::$prefix] ?? null);
    }

    public function get(string|array $key, string $fallback = "", ?array $sprint = null): ?string
    {
        if (is_null($this::$prefix)) {
            throw new Exception("Lang prefix is null.", 1);
        }
        if (!is_null($sprint)) {
            $this->sprint($sprint);
        }

        if (is_array($key)) {
            $out = array();
            foreach ($key as $k) {
                $out[] = $this->getValue($k);
            }
            return ucfirst(strtolower(implode(" ", $out)));
        }

        $value = ($this->value[$key][$this::$prefix] ?? $fallback);
        if (is_null($sprint)) {
            return $value;
        }

        return vsprintf(($this->value[$key][$this::$prefix] ?? $fallback), $this->sprint);
    }
}
