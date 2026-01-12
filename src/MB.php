<?php

/**
 * This is a Polyfill library for Multibyte
 * It is tho recommended to install mb on the server if is missing
 */

namespace MaplePHP\DTO;

use ErrorException;

class MB
{
    private string $value;
    private Iconv $iconv;
    private bool $disableVanilla = false;

    /**
     * The input string value
     *
     * @param string $value
     */
    public function __construct(string $value)
    {
        $this->value = $value;
        $this->iconv = new Iconv($this->value);
    }

    /**
     * Get value as string
     *
     * @return string
     */
    public function __toString(): string
    {
        return (string)$this->get();
    }

    /**
     * Get value
     * @return string|false
     */
    public function get(): string|false
    {
        return $this->value;
    }

    public function getValue(): string|false
    {
        return $this->get();
    }

    /**
     * Will disable vanilla iconv function, used mostly for testing.
     *
     * @param bool $disable
     * @return void
     */
    public function disableVanilla(bool $disable): void
    {
        $this->disableVanilla = $disable;
        $this->iconv->disableVanilla($disable);
    }

    /**
     * mb_convert_encoding polyfill (immutable)
     *
     * @param string $fromEncoding
     * @param string $toEncoding
     * @return $this
     * @throws ErrorException
     */
    public function encode(string $fromEncoding, string $toEncoding): self
    {
        $inst = clone $this;
        if (function_exists('mb_convert_encoding') && !$inst->disableVanilla) {
            $inst->value = mb_convert_encoding($inst->value, $toEncoding, $fromEncoding);
        } else {
            $inst->iconv = $inst->iconv->encode($fromEncoding, $toEncoding);
            $inst->value = $inst->iconv->getValue();
        }
        return $inst;
    }

    /**
     * mb_strlen polyfill
     *
     * @param string|null $encoding
     * @return int|false
     * @throws ErrorException
     */
    public function strlen(?string $encoding = null): int|false
    {
        if (function_exists('mb_strlen') && !$this->disableVanilla) {
            return mb_strlen($this->value, $encoding);
        }
        return $this->iconv->strlen($encoding);
    }

    /**
     * mb_substr polyfill
     *
     * @param int $start
     * @param int|null $length
     * @param string|null $encoding (e.g. UTF-8)
     * @return self
     * @throws ErrorException
     */
    public function substr(int $start, ?int $length = null, ?string $encoding = null): self
    {
        $inst = clone $this;
        if (function_exists('mb_substr') && !$inst->disableVanilla) {
            $inst->value =  mb_substr($inst->value, $start, $length, $encoding);
        } else {
            $this->iconv = $this->iconv->substr($start, $length, $encoding);
            $inst->value = $this->iconv->getValue();
        }
        return $inst;
    }

    /**
     * mb_strpos polyfill
     *
     * @param string $needle
     * @param int $offset
     * @param string|null $encoding
     * @return false|int
     * @throws ErrorException
     */
    public function strpos(string $needle, int $offset = 0, ?string $encoding = null): false|int
    {
        if (function_exists('mb_strpos') && !$this->disableVanilla) {
            return mb_strpos($this->value, $needle, $offset, $encoding);
        }
        return $this->iconv->strpos($needle, $offset, $encoding);
    }

    /**
     * mb_strrpos polyfill
     *
     * @param string $needle
     * @param string|null $encoding
     * @return false|int
     * @throws ErrorException
     */
    public function strrpos(string $needle, ?string $encoding = null): false|int
    {
        if (function_exists('mb_strrpos') && !$this->disableVanilla) {
            return mb_strrpos($this->value, $needle, 0, $encoding);
        }
        return $this->iconv->strrpos($needle, $encoding);
    }
}
