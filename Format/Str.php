<?php

/**
 * @Package:    MaplePHP Format string
 * @Author:     Daniel Ronkainen
 * @Licence:    Apache-2.0 license, Copyright © Daniel Ronkainen
                Don't delete this comment, its part of the license.
 */

namespace MaplePHP\DTO\Format;

use InvalidArgumentException;
use MaplePHP\DTO\MB;

final class Str extends FormatAbstract implements FormatInterface
{
    //protected $value;

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
        return (string)$this->value;
    }

    /**
     * Excerpt/shorten down text/string
     * @param integer $length total length
     * @param string $ending When break text add an ending (...)
     * @return self
     * @throws \ErrorException
     */
    public function excerpt(int $length = 40, string $ending = "..."): self
    {
        $this->stripTags()->entityDecode();
        $this->value = str_replace(array("'", '"', "”"), array("", "", ""), $this->strVal());
        $mb = new MB($this->value);
        $strlen = $mb->strlen();
        $this->value = trim($mb->substr(0, $length));
        if ($strlen > $length) {
            $this->value .= $ending;
        }
        return $this;
    }

    /**
     * Convert new line to html <br>
     * @return self
     */
    public function nl2br(): self
    {
        $this->value = nl2br($this->strVal());
        return $this;
    }

    /**
     * Make sure string always end with a trailing slash (will only add slash if it does not exist)
     * @return self
     */
    public function trailingSlash(): self
    {
        $this->value = rtrim($this->strVal(), "/") . '/';
        return $this;
    }

    /**
     * Strip html tags from string
     * @param  string $whitelist "<em><strong>"
     * @return self
     */
    public function stripTags(string $whitelist = ""): self
    {
        $this->value = strip_tags($this->strVal(), $whitelist);
        return $this;
    }

    /**
     * Cleans GET/POST data (XSS protection)
     * @param  int $flag ENT_QUOTES|ENT_SUBSTITUTE|ENT_HTML401|null
     * @return self
     */
    public function specialChars(int $flag = ENT_QUOTES): self
    {
        $this->value = htmlspecialchars($this->strVal(), $flag, 'UTF-8');
        return $this;
    }

    /**
     * Cleans GET/POST data (XSS protection)
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
     * @param  ?int $flag ENT_QUOTES|ENT_SUBSTITUTE|ENT_HTML401|null
     * @return self
     */
    public function decode(?int $flag = ENT_QUOTES): self
    {
        $this->value = htmlspecialchars_decode($this->strVal(), $flag);
        return $this;
    }

    /**
     * Clears soft breaks incl.:
     * line breaks (\n), carriage returns (\r), form feed (\f), and vertical tab (\v).
     * @return self
     */
    public function clearBreaks(): self
    {

        $this->value = preg_replace('/(\v|\s)+/', ' ', $this->strVal());
        return $this->trim();
    }

    /**
     * Clear all white spaces characters incl.: 
     * spaces, tabs, newline characters, carriage returns, and form feed characters
     * @return self
     */
    public function clearWhitespace(): self
    {
        $this->value = preg_replace('/\s+/', ' ', $this->strVal());
        return $this->trim();
    }

    /**
     * Entity Decode
     * @return self
     */
    public function entityDecode(): self
    {
        $this->value = html_entity_decode($this->strVal());
        return $this;
    }

    /**
     * Trim
     * @return self
     */
    public function trim(): self
    {
        $this->value = trim($this->strVal());
        return $this;
    }

    /**
     * String to lower
     * @return self
     */
    public function toLower(): self
    {
        $this->value = strtolower($this->strVal());
        return $this;
    }

    /**
     * String to upper
     * @return self
     */
    public function toUpper(): self
    {
        $this->value = strtoupper($this->strVal());
        return $this;
    }

    /**
     * Uppercase first
     * @return self
     */
    public function ucFirst(): self
    {
        $this->value = ucfirst($this->strVal());
        return $this;
    }

    /**
     * Add leading zero to string
     * @return self
     */
    public function leadingZero(): self
    {
        $this->value = str_pad($this->strVal(), 2, '0', STR_PAD_LEFT);
        return $this;
    }

    /**
     * Replace spaces
     * @param  string $replaceWith
     * @return self
     */
    public function replaceSpaces(string $replaceWith = "-"): self
    {
        $this->value = preg_replace("/\s/", $replaceWith, $this->strVal());
        return $this;
    }

    /**
     * Remove unwanted characters from string/mail and make it consistent
     * @return self
     */
    public function formatEmail(): self
    {
        return $this->trim()->replaceSpecialChar()->toLower();
    }


    /**
     * Replace multiple space between words with a single space
     * @return self
     */
    public function trimSpaces(): self
    {
        $this->value = preg_replace("/[\s-]+/", " ", $this->strVal());
        return $this;
    }

    /**
     * Remove unwanted characters from string/slug and make it consistent
     * @return self
     */
    public function formatSlug(): self
    {
        $this->clearBreaks()->trim()->replaceSpecialChar()->trimSpaces()->replaceSpaces()->tolower();
        $this->value = preg_replace("/[^a-z0-9\s-]/", "", $this->value);
        return $this;
    }

    /**
     * Replaces special characters to its counterpart to "A" or "O"
     * @return self
     */
    public function replaceSpecialChar(): self
    {
        $pattern = array('é','è','ë','ê','É','È','Ë','Ê','á','à','ä','â','å','Á','À','Ä','Â','Å',
            'ó','ò','ö','ô','Ó','Ò','Ö','Ô','í','ì','ï','î','Í','Ì','Ï','Î','ú','ù','ü','û','Ú',
            'Ù','Ü','Û','ý','ÿ','Ý','ø','Ø','œ','Œ','Æ','ç','Ç');
        $replace = array('e','e','e','e','E','E','E','E','a','a','a','a','a','A','A','A','A','A',
            'o','o','o','o','O','O','O','O','i','i','i','I','I','I','I','I','u','u','u','u','U',
            'U','U','U','y','y','Y','o','O','a','A','A','c','C');
        $this->value = str_replace($pattern, $replace, $this->strVal());

        return $this;
    }

    /**
     * Url decode
     * @return self
     */
    public function urlDecode(): self
    {
        $this->value = urldecode($this->strVal());
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
        $this->value = urlencode($this->strVal());
        if (!is_null($find) && !is_null($replace)) {
            $this->replace($find, $replace);
        }
        return $this;
    }

    /**
     * Raw url decode
     * @return self
     */
    public function rawUrlDecode(): self
    {
        $this->value = rawurldecode($this->strVal());
        return $this;
    }

    /**
     * Raw url encode (with string replace if you want)
     * @param array|null $find Search values
     * @param array|null $replace Replace values
     * @return self
     */
    public function rawUrlEncode(?array $find = null, ?array $replace = null): self
    {
        $this->value = rawurlencode($this->strVal());
        if (!is_null($find) && !is_null($replace)) {
            $this->replace($find, $replace);
        }
        return $this;
    }

    /**
     * String replace
     * @param array $find     Search values
     * @param array $replace  Replace values
     * @return self
     */
    public function replace(array $find, array $replace): self
    {
        $this->value = str_replace($find, $replace, $this->strVal());
        if(!is_string($this->value)) {
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
     * Explode return array instance
     * @param  non-empty-string $delimiter
     * @return Arr
     */
    public function explode(string $delimiter): Arr
    {
        return Arr::value(explode($delimiter, $this->value));
    }

    /**
     * Will convert all camelcase words to array and return array instance
     * @return Arr
     */
    public function camelCaseToArr(): Arr
    {
        return Arr::value(preg_split(
            '#([A-Z][^A-Z]*)#',
            $this->value,
            0,
            PREG_SPLIT_DELIM_CAPTURE | PREG_SPLIT_NO_EMPTY
        ));
    }


    /**
     * Extract path from URL
     * @return self
     */
    public function extractPath(): self
    {
        $this->value = (string)parse_url($this->value, PHP_URL_PATH);
        return $this;
    }

    /**
     * Get only dirname from path
     * @return self
     */
    public function dirname(): self
    {
        $this->value = dirname($this->value);
        return $this;
    }

    /**
     * Trim tailing slash
     * @return self
     */
    public function trimTrailingSlash(): self
    {
        $this->value = ltrim($this->value, '/');
        return $this;
    }

    /**
     * XXS protection
     * @return self
     */
    public function xxs(): self
    {
        if (is_null($this->value)) {
            $this->value = null;
        } else {
            $this->value = Str::value($this->value)->specialchars()->get();
        }
        return $this;
    }

    /**
     * To bool value
     * @return bool
     */
    public function toBool(): bool
    {
        if(is_numeric($this->value)) {
            return ((float)$this->value > 0);
        }
        return ($this->value !== "false" && strlen($this->value));
    }
    
    /**
     * Compare value to value 
     * @param  string|int|float|bool|null $compare
     * @return bool
     */
    public function compare(string|int|float|bool|null $compare): bool
    {
        if(is_numeric($this->value)) {
            return ((float)$this->value > 0);
        }
        return ($this->value === $compare);
    }

    /**
     * To int value
     * @return int
     */
    public function toInt(): int
    {
        return (int)$this->value;
    }

    /**
     * To float value
     * @return float
     */
    public function toFloat(): float
    {
        return (float)$this->value;
    }
}
