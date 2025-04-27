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
     * @return self
     * @throws ErrorException
     */
    public function position(string $needle, int $offset = 0, ?string $encoding = null): self
    {
        $inst = clone $this;
        $mb = new MB($this->raw);
        $inst->raw = $mb->strpos($needle, $offset, $encoding);
        return $inst;
    }

    /**
     * Find position of last occurrence of string in a string
     * This method uses multibyte functionality and provides a polyfill if your environment lacks support.
     *
     * @param string $needle
     * @param string|null $encoding (Example: 'UTF-8')
     * @return self
     * @throws ErrorException
     */
    public function positionLast(string $needle, ?string $encoding = null): self
    {
        $inst = clone $this;
        $mb = new MB($inst->raw);
        $inst->raw = $mb->strrpos($needle, $encoding);
        return $inst;
    }


    /**
     * Get part of string
     * This method uses multibyte functionality and provides a polyfill if your environment lacks support.
     *
     * @param int $start The start position of the substring
     * @param int|null $length The length of the substring. If null, extract all characters to the end
     * @param string|null $encoding The character encoding (e.g., 'UTF-8'). Default is null
     * @return self
     * @throws ErrorException
     */
    public function substr(int $start, ?int $length = null, ?string $encoding = null): self
    {
        $inst = clone $this;
        $mb = new MB($inst->raw);
        $inst->raw = (string)$mb->substr($start, $length, $encoding);
        return $inst;
    }

    /**
     * Get string length
     * This method uses multibyte functionality and provides a polyfill if your environment lacks support.
     *
     * @param string|null $encoding (Example: 'UTF-8')
     * @return self
     * @throws ErrorException
     */
    public function strlen(?string $encoding = null): self
    {
        $inst = clone $this;
        $mb = new MB($inst->raw);
        $inst->raw = $mb->strlen($encoding);
        return $inst;
    }

    /**
     * Checks if a string contains a given substring and returns bool to collection
     *
     * @param string $needle
     * @return self
     */
    public function contains(string $needle): self
    {
        $inst = clone $this;
        $inst->raw = str_contains($inst->raw, $needle);
        return $inst;
    }

    /**
     * Checks if a string starts with a given substring and returns bool to collection
     *
     * @param string $needle
     * @return self
     */
    public function startsWith(string $needle): self
    {
        $inst = clone $this;
        $inst->raw = str_starts_with($inst->raw, $needle);
        return $inst;
    }

    /**
     * Checks if a string ends with a given substring and returns bool to collection
     *
     * @param string $needle
     * @return self
     */
    public function endsWith(string $needle): self
    {
        $inst = clone $this;
        $inst->raw = str_ends_with($inst->raw, $needle);
        return $inst;
    }

    /**
     * Checks if a string contains a and return needle if true else false to collection
     *
     * @param string $needle
     * @return self
     */
    public function getContains(string $needle): self
    {
        $inst = clone $this;
        $inst->raw = $inst->contains($needle) ? $needle : false;
        return $inst;
    }


    /**
     * Get a substring that appears after the first occurrence of needle
     * Returns null if the needle is not found in the string
     *
     * @param string $needle The substring to search for
     * @param int $offset Additional offset to add after needle position (default: 0)
     * @return self
     * @throws ErrorException
     */
    public function getContainAfter(string $needle, int $offset = 0): self
    {
        $inst = clone $this;
        $position = $this->position($needle)->get();
        $inst->raw = ($position !== false) ? $inst->substr($position + 1 + $offset)->get() : null;
        return $inst;
    }

    /**
     * Checks if a string starts with a given substring and return needle if true else false
     *
     * @param string $needle
     * @return self
     */
    public function getStartsWith(string $needle): self
    {
        $inst = clone $this;
        $inst->raw = $inst->startsWith($needle) ? $needle : false;
        return $inst;
    }

    /**
     * Checks if a string ends with a given substring and return needle if true else false
     *
     * @param string $needle
     * @return self
     */
    public function getEndsWith(string $needle): self
    {
        $inst = clone $this;
        $inst->raw = $inst->endsWith($needle) ? $needle : false;
        return $inst;
    }

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
        $inst = clone $this;
        $inst->stripTags()->entityDecode();
        $inst->raw = str_replace(["'", '"', "”"], ["", "", ""], $inst->strVal());
        $mb = new MB($inst->raw);
        $strlen = $mb->strlen($encoding);
        $inst->raw = trim($mb->substr(0, $length, $encoding));
        if ($strlen > $length) {
            $inst->raw .= $ending;
        }
        return $inst;
    }

    /**
     * Convert new line to html <br>
     *
     * @return self
     */
    public function nl2br(): self
    {
        $inst = clone $this;
        $inst->raw = nl2br($inst->strVal());
        return $inst;
    }

    /**
     * Make sure string always end with a trailing slash (will only add slash if it does not exist)
     *
     * @return self
     */
    public function addTrailingSlash(): self
    {
        $inst = clone $this;
        $inst->raw = rtrim($inst->strVal(), "/") . '/';
        return $inst;
    }

    // Alias to trimTrailingSlash
    public function trailingSlash(): self
    {
        return $this->addTrailingSlash();
    }

    /**
     * Trim trailing slash
     *
     * @return self
     */
    public function trimTrailingSlash(): self
    {
        $inst = clone $this;
        $inst->raw = rtrim($inst->raw, '/');
        return $inst;
    }

    /**
     * Strip html tags from string
     *
     * @param  string $whitelist "<em><strong>"
     * @return self
     */
    public function stripTags(string $whitelist = ""): self
    {
        $inst = clone $this;
        $inst->raw = strip_tags($inst->strVal(), $whitelist);
        return $inst;
    }

    /**
     * HTML special characters encode
     *
     * @param  int $flag ENT_QUOTES|ENT_SUBSTITUTE|ENT_HTML401|null
     * @return self
     */
    public function encode(int $flag = ENT_QUOTES): self
    {
        return $this->specialChars($flag);
    }

    /**
     * HTML special characters decode
     *
     * @param  ?int $flag ENT_QUOTES|ENT_SUBSTITUTE|ENT_HTML401|null
     * @return self
     */
    public function decode(?int $flag = ENT_QUOTES): self
    {
        $inst = clone $this;
        $inst->raw = htmlspecialchars_decode($inst->strVal(), $flag);
        return $inst;
    }

    /**
     * HTML special characters encode
     *
     * @param int $flag ENT_QUOTES|ENT_SUBSTITUTE|ENT_HTML401|null
     * @param string $encoding
     * @return self
     */
    public function specialChars(int $flag = ENT_QUOTES, string $encoding = "UTF-8"): self
    {
        $inst = clone $this;
        $inst->raw = htmlspecialchars($inst->strVal(), $flag, $encoding);
        return $inst;
    }

    /**
     * Remove any character that is not a letter, number, underscore, or dash
     * Can be used to sanitize SQL identifiers that should be enclosed in backticks
     *
     * @return self
     */
    public function sanitizeIdentifiers(): self
    {
        $inst = clone $this;
        $inst->raw = preg_replace('/[^a-zA-Z0-9_-]/', '', $inst->raw);
        return $inst;
    }

    /**
     * Clears soft breaks incl.:
     * line breaks (\n), carriage returns (\r), form feed (\f), and vertical tab (\v).
     *
     * @return self
     */
    public function clearBreaks(): self
    {
        $inst = clone $this;
        $inst->raw = preg_replace('/(\v|\s)+/', ' ', $inst->strVal());
        return $inst->trim();
    }

    /**
     * Clear all white spaces characters incl.:
     * spaces, tabs, newline characters, carriage returns, and form feed characters
     *
     * @return self
     */
    public function normalizeSpaces(): self
    {
        $inst = clone $this;
        $inst->raw = preg_replace('/\s+/', ' ', $inst->strVal());
        return $inst->trim();
    }

    /**
     * Replace multiple space between words with a single space
     * Replaces multiple spaces, hyphens, and underscores with a single space.
     *
     * @return self
     */
    public function normalizeSeparators(): self
    {
        $inst = clone $this;
        $inst->raw = preg_replace("/[\s\-_]+/", " ", $inst->strVal());
        return $inst;
    }

    /**
     * Entity encode
     *
     * @param int $flags
     * @param string|null $encoding
     * @param bool $doubleEncode
     * @return self
     */
    public function entityEncode(int $flags = ENT_QUOTES|ENT_SUBSTITUTE, ?string $encoding = null, bool $doubleEncode = true): self
    {
        $inst = clone $this;
        $inst->raw = htmlentities($inst->strVal(), $flags, $encoding, $doubleEncode);
        return $inst;
    }

    /**
     * Entity Decode
     *
     * @param int $flags
     * @param string|null $encoding
     * @return self
     */
    public function entityDecode(int $flags = ENT_QUOTES|ENT_SUBSTITUTE, ?string $encoding = null): self
    {
        $inst = clone $this;
        $inst->raw = html_entity_decode($inst->strVal(), $flags, $encoding);
        return $inst;
    }

    /**
     * Trim the string, removing specified characters from the beginning and end.
     *
     * @param string $characters Characters to be trimmed (default: " \n\r\t\v\0").
     * @return self
     */
    public function trim(string $characters = " \n\r\t\v\0"): self
    {
        $inst = clone $this;
        $inst->raw = trim($inst->strVal(), $characters);
        return $inst;
    }

    /**
     * Strip whitespace (or other characters) from the beginning of a string
     *
     * @param string $characters Characters to be trimmed (default: " \n\r\t\v\0").
     * @return self
     */
    public function ltrim(string $characters = " \n\r\t\v\0"): self
    {
        $inst = clone $this;
        $inst->raw = ltrim($inst->strVal(), $characters);
        return $inst;
    }

    /**
     * Strip whitespace (or other characters) from the beginning of a string
     *
     * @param string $characters Characters to be trimmed (default: " \n\r\t\v\0").
     * @return self
     */
    public function rtrim(string $characters = " \n\r\t\v\0"): self
    {
        $inst = clone $this;
        $inst->raw = rtrim($inst->strVal(), $characters);
        return $inst;
    }

    /**
     * String to lower
     *
     * @return self
     */
    public function toLower(): self
    {
        $inst = clone $this;
        $inst->raw = strtolower($inst->strVal());
        return $inst;
    }

    /**
     * String to upper
     *
     * @return self
     */
    public function toUpper(): self
    {
        $inst = clone $this;
        $inst->raw = strtoupper($inst->strVal());
        return $inst;
    }

    /**
     * Uppercase the first letter in text
     *
     * @return self
     */
    public function ucFirst(): self
    {
        $inst = clone $this;
        $inst->raw = ucfirst($inst->strVal());
        return $inst;
    }

    /**
     * Uppercase the first letter in every word
     *
     * @return self
     */
    public function ucWords(): self
    {
        $inst = clone $this;
        $inst->raw = ucwords($inst->strVal());
        return $inst;
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
        $inst = clone $this;
        $inst->raw = str_pad($inst->strVal(), $length, $padString, $padType);
        return $inst;
    }

    /**
     * Pad string with leading zero
     *
     * @param int $length
     * @return self
     */
    public function leadingZero(int $length = 2): self
    {
        return $this->pad($length, '0', STR_PAD_LEFT);
    }

    /**
     * Replace spaces
     *
     * @param  string $replaceWith
     * @return self
     */
    public function replaceSpaces(string $replaceWith = "-"): self
    {
        $inst = clone $this;
        $inst->raw = preg_replace("/\s/", $replaceWith, $inst->strVal());
        return $inst;
    }

    /**
     * Remove unwanted characters from string/mail and make it consistent
     *
     * @return self
     */
    public function formatEmail(): self
    {
        return $this->trim()->normalizeAccents()->toLower();
    }

    /**
     * Remove unwanted characters from string/slug and make it consistent
     * @return self
     */
    public function slug(): self
    {
        $inst = $this
            ->clearBreaks()->normalizeAccents()->normalizeSeparators()
            ->trim()->replaceSpaces()->tolower();
        $inst->raw = preg_replace("/[^a-z0-9\s-]/", "", $inst->raw);
        return $inst;
    }

    // DEPRECATED: Alias to slug
    public function formatSlug(): self
    {
        return $this->slug();
    }

    /**
     * Replaces special characters to its counterpart to "A" or "O"
     *
     * @return self
     */
    public function normalizeAccents(): self
    {
        $inst = clone $this;
        $pattern = ['é','è','ë','ê','É','È','Ë','Ê','á','à','ä','â','å','Á','À','Ä','Â','Å',
            'ó','ò','ö','ô','Ó','Ò','Ö','Ô','í','ì','ï','î','Í','Ì','Ï','Î','ú','ù','ü','û','Ú',
            'Ù','Ü','Û','ý','ÿ','Ý','ø','Ø','œ','Œ','Æ','ç','Ç'];
        $replace = ['e','e','e','e','E','E','E','E','a','a','a','a','a','A','A','A','A','A',
            'o','o','o','o','O','O','O','O','i','i','i','I','I','I','I','I','u','u','u','u','U',
            'U','U','U','y','y','Y','o','O','a','A','A','c','C'];

        $inst->raw = str_replace($pattern, $replace, $inst->strVal());
        return $inst;
    }


    // DEPRECATED: Alias to normalizeAccents
    public function replaceSpecialChar(): self
    {
        return $this->normalizeAccents();
    }

    /**
     * Url decode
     *
     * @return self
     */
    public function urlDecode(): self
    {
        $inst = clone $this;
        $inst->raw = urldecode($inst->strVal());
        return $inst;
    }

    /**
     * Url encode (with string replace if you want)
     *
     * @return self
     */
    public function urlEncode(): self
    {
        $inst = clone $this;
        $inst->raw = urlencode($inst->strVal());
        return $inst;
    }

    /**
     * Raw url encode (with string replace if you want)
     *
     * @return self
     */
    public function rawUrlEncode(): self
    {
        $inst = clone $this;
        $inst->raw = rawurlencode($inst->strVal());
        return $inst;
    }

    /**
     * Raw url decode
     *
     * @return self
     */
    public function rawUrlDecode(): self
    {
        $inst = clone $this;
        $inst->raw = rawurldecode($inst->strVal());
        return $inst;
    }

    /**
     * String replace
     *
     * @param array|string $find Search values
     * @param array|string $replace Replace values
     * @return self
     */
    public function replace(array|string $find, array|string $replace): self
    {
        $inst = clone $this;
        $inst->raw = str_replace($find, $replace, $inst->strVal());
        if(!is_string($inst->raw)) {
            throw new InvalidArgumentException("The value has to be an string value!", 1);
        }
        return $inst;
    }

    /**
     * Decode then encode url (with string replace if you want)
     *
     * @return self
     */
    public function normalizeUrlEncoding(): self
    {
        return $this->urldecode()->rawurlencode();
    }

    // DEPRECATED: Alias to normalizeUrlEncoding
    public function toggleUrlEncode(): self
    {
        return $this->normalizeUrlEncoding();
    }

    /**
     * Will convert all camelcase words to array
     *
     * @return Str
     */
    public function explodeCamelCase(): self
    {
        $inst = clone $this;
        $inst->raw = preg_split('#([A-Z][^A-Z]*)#', $inst->raw, 0, PREG_SPLIT_DELIM_CAPTURE | PREG_SPLIT_NO_EMPTY);
        return $inst;
    }

    /**
     * Will convert all camelcase words to array and return Arr instance
     *
     * @return Arr
     */
    public function camelCaseToArr(): Arr
    {
        return Arr::value($this->explodeCamelCase()->get());
    }

    /**
     * Get URL path from URI
     *
     * @return self
     */
    public function getUrlPath(): self
    {
        $inst = clone $this;
        $inst->raw = (string)parse_url($inst->raw, PHP_URL_PATH);
        return $inst;
    }

    /**
     * Get URL scheme from URI
     *
     * @return self
     */
    public function getUrlScheme(): self
    {
        $inst = clone $this;
        $inst->raw = (string)parse_url($inst->raw, PHP_URL_SCHEME);
        return $inst;
    }

    /**
     * Get URL host from URI
     *
     * @return self
     */
    public function getUrlHost(): self
    {
        $inst = clone $this;
        $inst->raw = (string)parse_url($inst->raw, PHP_URL_HOST);
        return $inst;
    }

    /**
     * Get URL port from URI
     *
     * @return self
     */
    public function getUrlPort(): self
    {
        $inst = clone $this;
        $inst->raw = (string)parse_url($inst->raw, PHP_URL_PORT);
        return $inst;
    }

    /**
     * Get URL user from URI
     *
     * @return self
     */
    public function getUrlUser(): self
    {
        $inst = clone $this;
        $inst->raw = (string)parse_url($inst->raw, PHP_URL_USER);
        return $inst;
    }

    /**
     * Get URL password from URI
     *
     * @return self
     */
    public function getUrlPassword(): self
    {
        $inst = clone $this;
        $inst->raw = (string)parse_url($inst->raw, PHP_URL_PASS);
        return $inst;
    }

    /**
     * Get URL query string from URI
     *
     * @return self
     */
    public function getUrlQuery(): self
    {
        $inst = clone $this;
        $inst->raw = (string)parse_url($inst->raw, PHP_URL_QUERY);
        return $inst;
    }

    /**
     * Get URL fragment from URI
     *
     * @return self
     */
    public function getUrlFragment(): self
    {
        $inst = clone $this;
        $inst->raw = (string)parse_url($inst->raw, PHP_URL_FRAGMENT);
        return $inst;
    }

    /**
     * Get URL parts from URI
     *
     * @param array $parts
     * @return self
     */
    public function getUrlParts(array $parts): self
    {
        $arr = [];
        $inst = clone $this;
        foreach ($parts as $part) {
            $method = 'getUrl' . ucfirst($part);
            $subInst = new self($inst->raw);
            if(!method_exists($subInst, $method)) {
                throw new InvalidArgumentException("The part '$part' does not exist as a part in getUrlParts.", 1);
            }
            $subInst = call_user_func([$subInst, $method]);
            $arr[] = $subInst->get();
        }
        $inst->raw = $arr;
        return $inst;
    }

    /**
     * Get only dirname from path
     *
     * @return self
     */
    public function getDirname(): self
    {
        $inst = clone $this;
        $inst->raw = dirname($inst->raw);
        return $inst;
    }

    /**
     * Escape string value (protects against XSS)
     *
     * @return self
     */
    public function escape(): self
    {
        $inst = clone $this;
        if (is_null($inst->raw)) {
            $inst->raw = null;
        }
        return $inst->specialchars();
    }

    // Alias to 'escape'
    public function xss(): self
    {
        return $this->escape();
    }

    /**
     * Export a variable as a valid PHP string representation
     * This method uses PHP's var_export() function to get a parseable string representation
     * of the raw value
     *
     * @return self
     */
    public function varExport(): self
    {
        $inst = clone $this;
        $inst->raw = var_export($inst->raw, true);
        return $inst;
    }

    /**
     * Export raw value to string and escape special characters like newlines, tabs etc.
     * This method is used internally to get a readable string representation of the value.
     *
     * @return self
     */
    public function exportReadableValue(): self
    {
        return $this->replace(
            ["\n", "\r", "\t", "\v", "\0"],
            ['\\n', '\\r', '\\t', '\\v', '\\0']
        )->varExport()->replace('\\\\', '\\');
    }

    /**
     * Return a string to bool value
     *
     * @param bool|null $associative
     * @param int $depth
     * @param int $flags
     * @return self
     */
    public function jsonDecode(?bool $associative = null, int $depth = 512, int $flags = 0): self
    {
        $inst = clone $this;
        $inst->raw = json_decode($inst->raw, $associative, $depth, $flags);
        return $inst;
    }

    /**
     * Compare value to value
     *
     * @param  string|int|float|bool|null $compare
     * @return self
     */
    public function compare(string|int|float|bool|null $compare): self
    {
        $inst = clone $this;
        if(is_numeric($inst->raw)) {
            $inst->raw = ((float)$inst->raw > 0);
            return $inst;
        }
        $inst->raw = ($inst->raw === $compare);
        return $inst;
    }

}
