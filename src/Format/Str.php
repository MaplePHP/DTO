<?php

/**
 * @Package:    MaplePHP Format string
 * @Author:     Daniel Ronkainen
 * @Licence:    Apache-2.0 license, Copyright © Daniel Ronkainen
                Don't delete this comment, its part of the license.
 */

namespace MaplePHP\DTO\Format;

use ErrorException;
use InvalidArgumentException;
use MaplePHP\DTO\MB;

final class Str extends FormatAbstract implements FormatInterface
{
    /**
     * Input is mixed data type in the interface because I do not know the type before
     * The class constructor MUST handle the input validation
     * @param string $value
     */
    public function __construct(mixed $value)
    {
        if (is_array($value) || is_object($value)) {
            throw new InvalidArgumentException("Is expecting a string or a convertable string value.", 1);
        }
        parent::__construct((string)$value);
    }

    /**
     * Init format by adding data to modify/format/traverse
     * @param  mixed $value
     * @return self
     */
    public static function value(mixed $value): FormatInterface
    {
        return new Str((string)$value);
    }


    /**
     * Get value as string
     * @return string
     */
    public function strVal(): string
    {
        return (string)$this->raw;
    }

    /**
     * Find the position of the first occurrence of a substring in the string.
     * This method uses multibyte functionality and provides a polyfill if your environment lacks support.
     *
     * @param string $needle The substring to search for.
     * @param int $offset The position in the string to start searching.
     * @param string|null $encoding The character encoding to use (e.g., 'UTF-8'). Default is null.
     * @return false|int Returns the position of the first occurrence of the substring, or false if it is not found.
     * @throws ErrorException
     */
    public function position(string $needle, int $offset = 0, ?string $encoding = null): false|int
    {
        $mb = new MB($this->raw);
        return $mb->strpos($needle, $offset, $encoding);
    }

    /**
     * Find position of last occurrence of string in a string
     * This method uses multibyte functionality and provides a polyfill if your environment lacks support.
     *
     * @param string $needle
     * @param int $offset
     * @param string|null $encoding (Example: 'UTF-8')
     * @return false|int
     * @throws ErrorException
     */
    public function positionLast(string $needle, int $offset = 0, ?string $encoding = null): false|int
    {
        $mb = new MB($this->raw);
        return $mb->strrpos($needle, $offset, $encoding);
    }

    /**
     * Get string length
     * This method uses multibyte functionality and provides a polyfill if your environment lacks support.
     *
     * @param string|null $encoding (Example: 'UTF-8')
     * @return false|int
     * @throws ErrorException
     */
    public function strlen(?string $encoding = null): int|false
    {
        $mb = new MB($this->raw);
        return $mb->strlen($encoding);
    }

    /**
     * To int value
     * @return int
     */
    public function toInt(): int
    {
        return (int)$this->raw;
    }

    /**
     * To float value
     * @return float
     */
    public function toFloat(): float
    {
        return (float)$this->raw;
    }

    /**
     * Return a string to bool value
     *
     * @return bool
     */
    public function toBool(): bool
    {
        if(is_numeric($this->raw)) {
            return ((float)$this->raw > 0);
        }
        return ($this->raw !== "false" && strlen($this->raw));
    }

    /**
     * Return a string to bool value
     *
     * @param bool|null $associative
     * @param int $depth
     * @param int $flags
     * @return bool
     */
    public function jsonDecode(?bool $associative = null, int $depth = 512, int $flags = 0): bool
    {
        return json_decode($this->raw, $associative, $depth, $flags) !== null;
    }

    /**
     * Compare value to value
     *
     * @param  string|int|float|bool|null $compare
     * @return bool
     */
    public function compare(string|int|float|bool|null $compare): bool
    {
        if(is_numeric($this->raw)) {
            return ((float)$this->raw > 0);
        }
        return ($this->raw === $compare);
    }

    /**
     * Checks if a string ends with a given substring
     *
     * @param string $needle
     * @return bool
     */
    public function endsWith(string $needle): bool
    {
        return str_ends_with($this->raw, $needle);
    }

    /**
     * Checks if a string starts with a given substring
     *
     * @param string $needle
     * @return bool
     */
    public function startsWith(string $needle): bool
    {
        return str_starts_with($this->raw, $needle);
    }
    
    // Return self -->

    /**
     * Excerpt/shorten down text/string
     * This method uses multibyte functionality and provides a polyfill if your environment lacks support.
     * 
     * @param integer $length total length
     * @param string $ending When break text add an ending (...)
     * @param string|null $encoding
     * @return self
     * @throws ErrorException
     */
    public function excerpt(int $length = 40, string $ending = "...", ?string $encoding = null): self
    {
        $this->stripTags()->entityDecode();
        $this->raw = str_replace(["'", '"', "”"], ["", "", ""], $this->strVal());
        $mb = new MB($this->raw);
        $strlen = $mb->strlen($encoding);
        $this->raw = trim($mb->substr(0, $length, $encoding));
        if ($strlen > $length) {
            $this->raw .= $ending;
        }
        return $this;
    }

    /**
     * Convert new line to html <br>
     * 
     * @return self
     */
    public function nl2br(): self
    {
        $this->raw = nl2br($this->strVal());
        return $this;
    }

    /**
     * Make sure string always end with a trailing slash (will only add slash if it does not exist)
     * 
     * @return self
     */
    public function trailingSlash(): self
    {
        $this->raw = rtrim($this->strVal(), "/") . '/';
        return $this;
    }

    /**
     * Strip html tags from string
     * 
     * @param  string $whitelist "<em><strong>"
     * @return self
     */
    public function stripTags(string $whitelist = ""): self
    {
        $this->raw = strip_tags($this->strVal(), $whitelist);
        return $this;
    }

    /**
     * Cleans GET/POST data (XSS protection)
     * 
     * @param  int $flag ENT_QUOTES|ENT_SUBSTITUTE|ENT_HTML401|null
     * @return self
     */
    public function specialChars(int $flag = ENT_QUOTES): self
    {
        $this->raw = htmlspecialchars($this->strVal(), $flag, 'UTF-8');
        return $this;
    }

    /**
     * Remove any character that is not a letter, number, underscore, or dash
     * Can be used to sanitize SQL identifiers that should be enclosed in backticks
     * 
     * @return self
     */
    public function sanitizeIdentifiers(): self
    {
        $this->raw = preg_replace('/[^a-zA-Z0-9_-]/', '', $this->raw);
        return $this;
    }

    /**
     * Cleans GET/POST data (XSS protection)
     * 
     * @param  int $flag ENT_QUOTES|ENT_SUBSTITUTE|ENT_HTML401|null
     * @return self
     */
    public function encode(int $flag = ENT_QUOTES): self
    {
        $this->specialchars($flag);
        return $this;
    }

    /**
     * Decode html special characters
     * 
     * @param  ?int $flag ENT_QUOTES|ENT_SUBSTITUTE|ENT_HTML401|null
     * @return self
     */
    public function decode(?int $flag = ENT_QUOTES): self
    {
        $this->raw = htmlspecialchars_decode($this->strVal(), $flag);
        return $this;
    }

    /**
     * Clears soft breaks incl.:
     * line breaks (\n), carriage returns (\r), form feed (\f), and vertical tab (\v).
     * 
     * @return self
     */
    public function clearBreaks(): self
    {
        $this->raw = preg_replace('/(\v|\s)+/', ' ', $this->strVal());
        return $this->trim();
    }

    /**
     * Clear all white spaces characters incl.:
     * spaces, tabs, newline characters, carriage returns, and form feed characters
     * 
     * @return self
     */
    public function clearWhitespace(): self
    {
        $this->raw = preg_replace('/\s+/', ' ', $this->strVal());
        return $this->trim();
    }

    /**
     * Entity encode
     * 
     * @param int $flags
     * @param string|null $encoding
     * @param bool $double_encode
     * @return self
     */
    public function entityEncode(int $flags = ENT_QUOTES|ENT_SUBSTITUTE, ?string $encoding, bool $double_encode = true): self
    {
        $this->raw = htmlentities($this->strVal(), $flags, $encoding, $double_encode);
        return $this;
    }

    /**
     * Entity Decode
     * 
     * @param int $flags
     * @param string|null $encoding
     * @return self
     */
    public function entityDecode(int $flags = ENT_QUOTES|ENT_SUBSTITUTE, ?string $encoding): self
    {
        $this->raw = html_entity_decode($this->strVal(), $flags, $encoding);
        return $this;
    }

    /**
     * Trim the string, removing specified characters from the beginning and end.
     *
     * @param string $characters Characters to be trimmed (default: " \n\r\t\v\0").
     * @return self
     */
    public function trim(string $characters = " \n\r\t\v\0"): self
    {
        $this->raw = trim($this->strVal(), $characters);
        return $this;
    }

    /**
     * String to lower
     * 
     * @return self
     */
    public function toLower(): self
    {
        $this->raw = strtolower($this->strVal());
        return $this;
    }

    /**
     * String to upper
     * 
     * @return self
     */
    public function toUpper(): self
    {
        $this->raw = strtoupper($this->strVal());
        return $this;
    }

    /**
     * Uppercase first
     * 
     * @return self
     */
    public function ucFirst(): self
    {
        $this->raw = ucfirst($this->strVal());
        return $this;
    }

    /**
     * Pad a string to a certain length with another string
     *
     * @param int $length
     * @param string $padString
     * @param int $padType
     * @return self
     */
    public function pad(int $length, string $padString = " ", int $padType = STR_PAD_RIGHT): self
    {
        $this->raw = str_pad($this->strVal(), $length, $padString, $padType);
        return $this;
    }

    /**
     * Pad string with leading zero
     *
     * @return self
     */
    public function leadingZero(): self
    {
        return $this->pad(2, '0', STR_PAD_LEFT);
    }

    /**
     * Replace spaces
     * 
     * @param  string $replaceWith
     * @return self
     */
    public function replaceSpaces(string $replaceWith = "-"): self
    {
        $this->raw = preg_replace("/\s/", $replaceWith, $this->strVal());
        return $this;
    }

    /**
     * Remove unwanted characters from string/mail and make it consistent
     * 
     * @return self
     */
    public function formatEmail(): self
    {
        return $this->trim()->replaceSpecialChar()->toLower();
    }

    /**
     * Replace multiple space between words with a single space
     * 
     * @return self
     */
    public function trimSpaces(): self
    {
        $this->raw = preg_replace("/[\s-]+/", " ", $this->strVal());
        return $this;
    }

    /**
     * Remove unwanted characters from string/slug and make it consistent
     * @return self
     */
    public function formatSlug(): self
    {
        $this->clearBreaks()->trim()->replaceSpecialChar()->trimSpaces()->replaceSpaces()->tolower();
        $this->raw = preg_replace("/[^a-z0-9\s-]/", "", $this->raw);
        return $this;
    }

    /**
     * Replaces special characters to its counterpart to "A" or "O"
     * @return self
     */
    public function replaceSpecialChar(): self
    {
        $pattern = ['é','è','ë','ê','É','È','Ë','Ê','á','à','ä','â','å','Á','À','Ä','Â','Å',
            'ó','ò','ö','ô','Ó','Ò','Ö','Ô','í','ì','ï','î','Í','Ì','Ï','Î','ú','ù','ü','û','Ú',
            'Ù','Ü','Û','ý','ÿ','Ý','ø','Ø','œ','Œ','Æ','ç','Ç'];
        $replace = ['e','e','e','e','E','E','E','E','a','a','a','a','a','A','A','A','A','A',
            'o','o','o','o','O','O','O','O','i','i','i','I','I','I','I','I','u','u','u','u','U',
            'U','U','U','y','y','Y','o','O','a','A','A','c','C'];
        $this->raw = str_replace($pattern, $replace, $this->strVal());

        return $this;
    }

    /**
     * Url decode
     * @return self
     */
    public function urlDecode(): self
    {
        $this->raw = urldecode($this->strVal());
        return $this;
    }

    /**
     * Url encode (with string replace if you want)
     * @param array|null $find Search values
     * @param array|null $replace Replace values
     * @return self
     */
    public function urlEncode(?array $find = null, ?array $replace = null): self
    {
        $this->raw = urlencode($this->strVal());
        if (!is_null($find) && !is_null($replace)) {
            $this->replace($find, $replace);
        }
        return $this;
    }

    /**
     * Raw url decode
     * 
     * @return self
     */
    public function rawUrlDecode(): self
    {
        $this->raw = rawurldecode($this->strVal());
        return $this;
    }

    /**
     * Raw url encode (with string replace if you want)
     * 
     * @param array|null $find Search values
     * @param array|null $replace Replace values
     * @return self
     */
    public function rawUrlEncode(?array $find = null, ?array $replace = null): self
    {
        $this->raw = rawurlencode($this->strVal());
        if (!is_null($find) && !is_null($replace)) {
            $this->replace($find, $replace);
        }
        return $this;
    }

    /**
     * String replace
     * 
     * @param array $find     Search values
     * @param array $replace  Replace values
     * @return self
     */
    public function replace(array $find, array $replace): self
    {
        $this->raw = str_replace($find, $replace, $this->strVal());
        if(!is_string($this->raw)) {
            throw new InvalidArgumentException("The value has to be an string value!", 1);
        }
        return $this;
    }

    /**
     * Decode then encode url (with string replace if you want)
     * @param array|null $find Search values
     * @param array|null $replace Replace values
     * @return self
     */
    public function toggleUrlEncode(?array $find = null, ?array $replace = null): self
    {
        return $this->urldecode()->rawurlencode($find, $replace);
    }

    /**
     * Will convert all camelcase words to array and return array instance
     * 
     * @return Arr
     */
    public function camelCaseToArr(): Arr
    {
        return Arr::value(preg_split(
            '#([A-Z][^A-Z]*)#',
            $this->raw,
            0,
            PREG_SPLIT_DELIM_CAPTURE | PREG_SPLIT_NO_EMPTY
        ));
    }

    /**
     * Extract path from URL
     * 
     * @return self
     */
    public function extractPath(): self
    {
        $this->raw = (string)parse_url($this->raw, PHP_URL_PATH);
        return $this;
    }

    /**
     * Get only dirname from path
     * 
     * @return self
     */
    public function dirname(): self
    {
        $this->raw = dirname($this->raw);
        return $this;
    }

    /**
     * Trim tailing slash
     * 
     * @return self
     */
    public function trimTrailingSlash(): self
    {
        $this->raw = ltrim($this->raw, '/');
        return $this;
    }

    /**
     * Escape string value (protects against XSS)
     * 
     * @return self
     */
    public function escape(): self
    {
        if (is_null($this->raw)) {
            $this->raw = null;
        } else {
            $this->raw = Str::value($this->raw)->specialchars()->get();
        }
        return $this;
    }

    // Alias to 'escape'
    public function xss(): self
    {
        return $this->escape();
    }
    
}
