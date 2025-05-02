<?php

/**
 * This is a Polyfill library for iconv
 * It is tho recommended to install iconv on the server rather than using the polyfill
 * but in cases this is not possible then this library will save you in a clinch
 *
 * The polyfill map files comes from Symfony but the library itself has been completely re-built
 */

namespace MaplePHP\DTO;

use ErrorException;
use ValueError;

use function strlen;

class Iconv
{
    public const ULEN_MASK = ["\xC0" => 2, "\xD0" => 2, "\xE0" => 3, "\xF0" => 4];
    public const STRING_MAX_LENGTH = 2147483647;
    private string|false $value;
    private bool $disableVanilla = false;
    private bool $strposFollowThrough = false;
    private string $encoding = "utf-8";

    /**
     * The input string value
     *
     * @param string $value
     */
    public function __construct(string $value)
    {
        $this->value = $value;
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
    }

    /**
     * iconv_strlen polyfill (immutable)
     * Convert a string from one character encoding to another
     * https://www.php.net/manual/en/function.iconv.php
     *
     * @throws ErrorException
     */
    public function encode(string $fromEncoding, string $toEncoding): self
    {
        $inst = $this->setEncoding($toEncoding);
        if (function_exists('iconv') && !$inst->disableVanilla) {
            $inst->value = iconv($fromEncoding, $toEncoding, $inst->value);

        } else {
            $fromEncoding = strtolower($fromEncoding);
            $toEncoding = strtolower($toEncoding);
            $translit = $inst->clearSuffix($toEncoding, '//translit');
            $ignore = $inst->clearSuffix($toEncoding, '//ignore');
            /*
            if (!$ignore && $error) {
                throw new ErrorException("iconv(): Detected an illegal character in input string");
            }
             */
            if ($fromEncoding !== "utf-8") {
                // Convert input to UTF-8
                $inMap = $inst->getMap("from", $fromEncoding);
                $inst->value = $inst->encodeUtf8($inMap, $inst->value);
                if ($inMap === false) {
                    throw new ErrorException('iconv_strlen(): Wrong encoding, conversion from "' . $fromEncoding . '"');
                }

            } elseif ('utf-8' === $toEncoding) {
                // UTF-8 validation
                $inst->value = $inst->getSanitizedUTF8String($inst->value);
            }

            if ($toEncoding !== 'utf-8') {
                $outMap = $inst->getMap("to", $toEncoding);
                $inst->value = $inst->decodeUtf8($inst->value, $outMap, ($translit ? $inst->getMapFile("translit") : []));
                if ($outMap === false) {
                    throw new ErrorException('iconv_strlen(): Wrong encoding, conversion to "' . $toEncoding . '"');
                }
            }
        }
        return $inst;
    }

    protected function setEncoding(?string $encoding = null): self
    {
        $inst = clone $this;
        if (is_string($encoding)) {
            $inst->encoding = $encoding;
        }
        return $inst;
    }

    /**
     * iconv_strlen polyfill -
     * Returns the character count of string
     * https://www.php.net/manual/en/function.iconv-strlen.php
     *
     * @param string|null $encoding
     * @return int|false
     * @throws ErrorException
     */
    public function strlen(?string $encoding = null): int|false
    {
        $inst = $this->setEncoding($encoding);
        if (function_exists("iconv_strlen") && !$inst->disableVanilla) {
            return iconv_strlen($inst->value, $inst->encoding);
        }
        if (is_string($encoding)) {
            $inst = $this->encode("utf-8", $inst->encoding);
            if ($inst->getValue() === false) {
                return false;
            }
        }
        return $inst->loop($inst->value);
    }

    /**
     * iconv_substr polyfill (immutable) -
     * Cut out part of a string
     * https://www.php.net/manual/it/function.iconv-substr.php
     *
     * @param int $start
     * @param int|null $length
     * @param string|null $encoding
     * @return self
     * @throws ErrorException
     */
    public function substr(int $start, ?int $length = null, ?string $encoding = null): self
    {
        $value = "";
        $inst = $this->setEncoding($encoding);
        $length = $inst->getLength($length);
        if (function_exists("iconv_substr") && !$inst->disableVanilla) {
            $value = (string)iconv_substr($inst->value, $start, $length, $inst->encoding);

        } else {
            if (is_string($encoding)) {
                $inst = $inst->encode("utf-8", $inst->encoding);
            }
            $inc = 0;
            $inst->loop($inst->value, function ($character, $charCount) use (&$value, &$inc, $start, $length) {
                if (($charCount + 1) > $start) {
                    $value .= $character;
                    $inc++;
                    if ($inc >= $length) {
                        return $inc;
                    }
                }
                return false;
            });
        }

        $inst->value = $value;
        return $inst;
    }

    /**
     * iconv_strpos polyfill -
     * Finds position of first occurrence of a needle within a haystack
     * https://www.php.net/manual/en/function.iconv-strpos.php
     *
     * @param string $needle
     * @param int $offset
     * @param string|null $encoding
     * @return false|int
     * @throws ErrorException
     */
    public function strpos(string $needle, int $offset = 0, ?string $encoding = null): false|int
    {

        $inst = $this->setEncoding($encoding);
        $inc = 0;
        $total = 0;
        $completed = false;
        if (function_exists("iconv_strpos") && !$inst->disableVanilla) {
            return iconv_strpos($inst->value, $needle, $offset, $inst->encoding);
        }
        if (is_string($encoding)) {
            $inst = $inst->encode("utf-8", $inst->encoding);
        }
        $needleInst = new self($needle);
        if (is_string($encoding)) {
            $needleInst->encode("utf-8", $inst->encoding);
        }
        $needleLength = $needleInst->strlen();

        if ($offset < 0) {
            $offset = ($inst->strlen() + $offset);
        }

        $inst->loop($inst->value, function (string $character, int $charCount) use (
            &$inc,
            &$total,
            &$completed,
            $needleLength,
            $needleInst,
            $offset,
            $encoding
        ) {

            if (($charCount + 1) > $offset) {
                $char = (string)$needleInst->substr($inc, 1);
                if ($character === $char) {
                    $inc++;
                    if ($inc === $needleLength) {
                        $completed = ($charCount + 1) - $inc;
                        if (!$this->strposFollowThrough) {
                            return $completed;
                        }
                    }
                } else {
                    $inc = 0;
                }
            }
            $total++;
            return false;
        });
        if ($offset > $total) {
            throw new ValueError('iconv_strpos(): Argument #3 ($offset) must be contained in argument #1 ($haystack)');
        }
        return ($completed && $inc > 0) ? $completed : false;
    }

    /**
     * iconv_strpos polyfill -
     * Finds position of first occurrence of a needle within a haystack
     * https://www.php.net/manual/en/function.iconv-strpos.php
     *
     * @param string $needle
     * @param string|null $encoding
     * @return false|int
     * @throws ErrorException
     */
    public function strrpos(string $needle, ?string $encoding = null): false|int
    {
        $inst = $this->setEncoding($encoding);
        $inst = $inst->strposFollowThrough(true);
        if (function_exists("iconv_strrpos") && !$inst->disableVanilla) {
            return iconv_strrpos($inst->value, $needle, $inst->encoding);
        }
        return $inst->strpos($needle, 0, $encoding);
    }

    /**
     * Will clear suffix from string and return bool
     *
     * @param string $string
     * @param string $suffix
     * @return bool
     */
    public function clearSuffix(string &$string, string $suffix): bool
    {
        $length = strlen($suffix);
        if (substr($string, -$length) === $suffix) {
            $string = substr($string, 0, -$length);
            return true;
        }
        return false;

    }

    /**
     * Will loop to the end without breaking the loop
     *
     * @param bool $followThrough
     * @return $this
     */
    final protected function strposFollowThrough(bool $followThrough): self
    {
        $inst = clone $this;
        $inst->strposFollowThrough = $followThrough;
        return $inst;
    }

    /**
     * Will get map array
     *
     * @param string $type
     * @param string $charset
     * @return array|false
     */
    private function getMap(string $type, string $charset): array|false
    {
        if ($map = $this->getMapFile("from.$charset")) {
            if ($type === "to") {
                return array_flip($map);
            }
            return $map;
        }
        return false;
    }

    /**
     * Will open map from file
     *
     * @param $file
     * @return array|null
     */
    private function getMapFile($file): ?array
    {
        $file = __DIR__ . '/Map/' .$file.'.php';
        if (is_file($file)) {
            return require $file;
        }
        return null;
    }

    /**
     * Will decode and process UTF-8 char from map array
     *
     * @param array $map
     * @param string $string
     * @return string
     */
    protected function encodeUtf8(array $map, string $string): string
    {
        $result = "";
        $length = strlen($string);
        for ($inc = 0; $inc < $length; ++$inc) {
            if (isset($string[$inc + 1], $map[$string[$inc].$string[$inc + 1]])) {
                $result .= $map[$string[$inc].$string[++$inc]];
            } elseif (isset($map[$string[$inc]])) {
                $result .= $map[$string[$inc]];
            }
        }
        return $result;
    }

    /**
     * Will decode and process UTF-8 char from map array
     *
     * @param $input
     * @param $map
     * @param array $translitMap
     * @return string|false
     */
    protected function decodeUtf8($input, $map, array $translitMap = []): string|false
    {
        $result = '';
        $input = $this->getSanitizedUTF8String($input);
        $length = strlen($input);
        for ($i = 0; $i < $length; $i++) {
            $char = $input[$i];

            // Handle UTF-8 multibyte sequences
            if (ord($char) >= 0x80) {
                $value = ord($char);
                if (($value & 0xE0) == 0xC0) {
                    $byteCount = 1;
                } elseif (($value & 0xF0) == 0xE0) {
                    $byteCount = 2;
                } elseif (($value & 0xF8) == 0xF0) {
                    $byteCount = 3;
                } else {
                    // Invalid UTF-8 start byte, append '?'
                    $result .= '?';
                    continue;
                }

                // Read additional bytes
                for ($j = 0; $j < $byteCount; $j++) {
                    $i++;
                    if (!($i < $length)) {
                        $result .= '?';
                        continue 2;
                    }
                }
                $result .= '?';


            } else {
                // ASCII character
                if (isset($map[$char])) {
                    $result .= $map[$char];
                } elseif (isset($translitMap[$char])) {
                    $result .= $translitMap[$char];
                } else {
                    $result .= $char;
                }
            }
        }

        return $result;
    }

    /**
     * Processes and sanitized an entire UTF-8 string, ensuring that it contains only valid UTF-8 sequences.
     *
     * @param $string
     * @return string
     */
    protected function getSanitizedUTF8String($string): string
    {
        $result = '';
        $len = strlen($string);

        for ($i = 0; $i < $len; $i++) {
            $byte = ord($string[$i]);

            if ($byte <= 0x7F) {
                // Single-byte character (ASCII)
                $result .= chr($byte);
            } elseif (($byte & 0xE0) == 0xC0) {
                // Two-byte character
                if ($i + 1 < $len && (ord($string[$i + 1]) & 0xC0) == 0x80) {
                    $result .= $string[$i] . $string[$i + 1];
                    $i++;
                }
            } elseif (($byte & 0xF0) == 0xE0) {
                // Three-byte character
                if ($i + 2 < $len && (ord($string[$i + 1]) & 0xC0) == 0x80 && (ord($string[$i + 2]) & 0xC0) == 0x80) {
                    $result .= $string[$i] . $string[$i + 1] . $string[$i + 2];
                    $i += 2;
                }
            } elseif (($byte & 0xF8) == 0xF0) {
                // Four-byte character
                if ($i + 3 < $len && (ord($string[$i + 1]) & 0xC0) == 0x80 && (ord($string[$i + 2]) & 0xC0) == 0x80 && (ord($string[$i + 3]) & 0xC0) == 0x80) {
                    $result .= $string[$i] . $string[$i + 1] . $string[$i + 2] . $string[$i + 3];
                    $i += 3;
                }
            }
        }
        return $result;
    }

    /**
     * Loop through characters (not bytes)
     *
     * @param string $value
     * @param callable|null $call
     * @return int
     */
    protected function loop(string $value, ?callable $call = null): int
    {
        $int = 0;
        $charCount = 0;
        $bytes = strlen($value);
        while ($int < $bytes) {
            $ulen = $value[$int] & "\xF0";
            $length = self::ULEN_MASK[$ulen] ?? 1;
            // Extract the character
            $character = substr($value, $int, $length);
            // Move to the next character
            $int += $length;

            if (is_callable($call)) {
                $returnValue = $call($character, $charCount, $int);
                if ($returnValue !== false) {
                    $charCount = $returnValue;
                    break;
                }
            }
            $charCount++;
        }
        return $charCount;
    }

    /**
     * Detected if string has illegal character
     *
     * @param string $value
     * @return bool
     */
    final public function hasIllegalChar(string $value): bool
    {
        return (bool)preg_match('//u', $value);
    }

    /**
     * Will return an expected int length value
     *
     * @param int|null $length
     * @return int
     */
    final public function getLength(?int $length = null): int
    {
        return (is_null($length) || $length > self::STRING_MAX_LENGTH) ? self::STRING_MAX_LENGTH : $length;
    }
}
