<?php

/**
 * @Package:    MaplePHP - The main traverse class
 * @Author:     Daniel Ronkainen
 * @Licence:    Apache-2.0 license, Copyright © Daniel Ronkainen
                Don't delete this comment, it's part of the license.
 */

namespace MaplePHP\DTO;

use BadMethodCallException;
use Closure;
use ErrorException;
use MaplePHP\DTO\Format\FormatInterface;
use MaplePHP\DTO\Format\Str;
use MaplePHP\Validate\Validator;
use ReflectionClass;
use ReflectionException;
use stdClass;

/**
 * @method self strStrVal()
 * @method self strPosition(string $needle, int $offset = '0', ?string $encoding = '')
 * @method self strPositionLast(string $needle, ?string $encoding = '')
 * @method self strSubstr(int $start, ?int $length = '', ?string $encoding = '')
 * @method self strStrlen(?string $encoding = '')
 * @method self strContains(string $needle)
 * @method self strStartsWith(string $needle)
 * @method self strEndsWith(string $needle)
 * @method self strGetContains(string $needle)
 * @method self strGetContainAfter(string $needle, int $offset = '0')
 * @method self strGetStartsWith(string $needle)
 * @method self strGetEndsWith(string $needle)
 * @method self strExcerpt(int $length = '40', string $ending = '...', ?string $encoding = '')
 * @method self strNl2br()
 * @method self strAddTrailingSlash()
 * @method self strTrailingSlash()
 * @method self strTrimTrailingSlash()
 * @method self strStripTags(string $whitelist = '')
 * @method self strEncode(int $flag = '3')
 * @method self strDecode(?int $flag = '3')
 * @method self strSpecialChars(int $flag = '3', string $encoding = 'UTF-8')
 * @method self strSanitizeIdentifiers()
 * @method self strClearBreaks()
 * @method self strNormalizeSpaces()
 * @method self strNormalizeSeparators()
 * @method self strEntityEncode(int $flags = '11', ?string $encoding = '', bool $doubleEncode = '1')
 * @method self strEntityDecode(int $flags = '11', ?string $encoding = '')
 * @method self strTrim(string $characters = ' \n\r\t\v\0')
 * @method self strLtrim(string $characters = ' \n\r\t\v\0')
 * @method self strRtrim(string $characters = ' \n\r\t\v\0')
 * @method self strToLower()
 * @method self strToUpper()
 * @method self strUcFirst()
 * @method self strUcWords()
 * @method self strPad(int $length, string $padString = ' ', int $padType = '1')
 * @method self strLeadingZero(int $length = '2')
 * @method self strReplaceSpaces(string $replaceWith = '-')
 * @method self strFormatEmail()
 * @method self strSlug()
 * @method self strFormatSlug()
 * @method self strNormalizeAccents()
 * @method self strReplaceSpecialChar()
 * @method self strUrlDecode()
 * @method self strUrlEncode()
 * @method self strRawUrlEncode()
 * @method self strRawUrlDecode()
 * @method self strReplace(array|string $find, array|string $replace)
 * @method self strNormalizeUrlEncoding()
 * @method self strToggleUrlEncode()
 * @method self strExplodeCamelCase()
 * @method self strCamelCaseToArr()
 * @method self strGetUrlPath()
 * @method self strGetUrlScheme()
 * @method self strGetUrlHost()
 * @method self strGetUrlPort()
 * @method self strGetUrlUser()
 * @method self strGetUrlPassword()
 * @method self strGetUrlQuery()
 * @method self strGetUrlFragment()
 * @method self strGetUrlParts(array $parts)
 * @method self strGetDirname()
 * @method self strEscape()
 * @method self strXss()
 * @method self strVarExport()
 * @method self strExportReadableValue()
 * @method self strJsonDecode(?bool $associative = '', int $depth = '512', int $flags = '0')
 * @method self strCompare(string|int|float|bool|null $compare)
 * @method self strGet()
 * @method self strFallback(string $fallback)
 * @method self strClone()
 * @method self strDto(string $dtoClassName)
 * @method self numSetLocale(string $locale, int $type)
 * @method self numGetNumFormatter()
 * @method self numFloat()
 * @method self numInt()
 * @method self numRound(int $precision, int $mode)
 * @method self numFloor()
 * @method self numCeil()
 * @method self numAbs()
 * @method self numNumberFormat(int $decimals, string $decimalSeparator, string $thousandsSeparator)
 * @method self numLeadingZero(int $length)
 * @method self numClamp(float $min, float $max)
 * @method self numIsEven()
 * @method self numIsOdd()
 * @method self numPercentToDecimal()
 * @method self numToPercent(int $precision)
 * @method self numToKb()
 * @method self numToFilesize()
 * @method self numToBytes()
 * @method self numToCurrency(string $currency, int $decimals, int $roundingMode)
 * @method self numGetCurrencySymbol(string $currency)
 * @method self numToCurrencyIso(string $currency, int $decimals)
 * @method self numGet()
 * @method self numFallback(string $fallback)
 * @method self numClone()
 * @method self numDto(string $dtoClassName)
 * @method self clockSetLocale(string $locale, int $type)
 * @method self clockGetNumFormatter()
 * @method self clockFloat()
 * @method self clockInt()
 * @method self clockRound(int $precision, int $mode)
 * @method self clockFloor()
 * @method self clockCeil()
 * @method self clockAbs()
 * @method self clockNumberFormat(int $decimals, string $decimalSeparator, string $thousandsSeparator)
 * @method self clockLeadingZero(int $length)
 * @method self clockClamp(float $min, float $max)
 * @method self clockIsEven()
 * @method self clockIsOdd()
 * @method self clockPercentToDecimal()
 * @method self clockToPercent(int $precision)
 * @method self clockToKb()
 * @method self clockToFilesize()
 * @method self clockToBytes()
 * @method self clockToCurrency(string $currency, int $decimals, int $roundingMode)
 * @method self clockGetCurrencySymbol(string $currency)
 * @method self clockToCurrencyIso(string $currency, int $decimals)
 * @method self clockGet()
 * @method self clockFallback(string $fallback)
 * @method self clockClone()
 * @method self clockDto(string $dtoClassName)
 * @method self domSetLocale(string $locale, int $type)
 * @method self domGetNumFormatter()
 * @method self domFloat()
 * @method self domInt()
 * @method self domRound(int $precision, int $mode)
 * @method self domFloor()
 * @method self domCeil()
 * @method self domAbs()
 * @method self domNumberFormat(int $decimals, string $decimalSeparator, string $thousandsSeparator)
 * @method self domLeadingZero(int $length)
 * @method self domClamp(float $min, float $max)
 * @method self domIsEven()
 * @method self domIsOdd()
 * @method self domPercentToDecimal()
 * @method self domToPercent(int $precision)
 * @method self domToKb()
 * @method self domToFilesize()
 * @method self domToBytes()
 * @method self domToCurrency(string $currency, int $decimals, int $roundingMode)
 * @method self domGetCurrencySymbol(string $currency)
 * @method self domToCurrencyIso(string $currency, int $decimals)
 * @method self domGet()
 * @method self domFallback(string $fallback)
 * @method self domClone()
 * @method self domDto(string $dtoClassName)
 * @method \MaplePHP\DTO\Format\Str str()
 * @method \MaplePHP\DTO\Format\Arr arr()
 * @method \MaplePHP\DTO\Format\Num num()
 * @method \MaplePHP\DTO\Format\Clock clock()
 * @method \MaplePHP\DTO\Format\Dom dom()
 * @method \MaplePHP\DTO\Format\Encode encode()
 * @method \MaplePHP\DTO\Format\Local local()
 */
class Traverse extends DynamicDataAbstract implements TraverseInterface
{
    use Traits\CollectionUtilities;

    protected mixed $raw = null;

    private static array $helpers = [
      'Str', 'Arr', 'Num', 'Clock', 'Dom', 'Encode', 'Local'
    ];

    public function __construct(mixed $data = null)
    {
        parent::__construct();
        $this->build($data);
    }

    /**
     * Init instance
     *
     * @param mixed $data
     * @return self
     */
    public static function value(mixed $data): self
    {
        return new self($data);
    }

    /**
     * With a new "Traverse" collection
     *
     * @param mixed $data
     * @return self
     */
    public function with(mixed $data): self
    {
        if ($data instanceof TraverseInterface) {
            return clone $data;
        }
        return new self($data);
    }

    /**
     * Add custom Helpers
     *
     * @param FormatInterface $helper
     * @return void
     */
    public function addHelper(FormatInterface $helper): void
    {
        self::$helpers[] = $helper;
    }

    /**
     * List all supported Helpers classes
     *
     * @return string[]
     */
    public static function listAllHelpers(): array
    {
        return self::$helpers;
    }

    /**
     * Object traverser
     *
     * @param $key
     * @return Traverse
     */
    public function __get($key): self
    {
        if (isset($this->getData()->{$key})) {
            $data = $this->getData()->{$key};
            if (is_object($data) && !($data instanceof DynamicDataAbstract)) {
                return $data;
            }
            return $this::value($data);
        }

        if (
            (is_array($this->raw) && isset($this->raw[$key])) ||
            (is_object($this->raw) && isset($this->raw->{$key}))
        ) {
            return $this::value(
                is_array($this->raw) ? $this->raw[$key] : $this->raw->{$key}
            );
        }

        $this->raw = null;
        return $this;
    }

    /**
     * Immutable formating class
     *
     * @param $method
     * @param $args
     * @return self
     * @throws ReflectionException|BadMethodCallException
     */
    public function __call($method, $args)
    {
        $inst = clone $this;
        $data = Str::value($method)->camelCaseToArr()->get();
        //$data = [$method];
        $expectedClass = array_shift($data);
        $formatClassInst = $this->format($expectedClass, $this->raw);
        $expectedMethod = implode('', $data);
        if (!$expectedMethod) {
            return $formatClassInst;
        }
        $expectedMethod = lcfirst($expectedMethod);

        if (!method_exists($formatClassInst, $expectedMethod) &&
            ($formatClassInst === "Collection" && !function_exists($expectedMethod))) {
            throw new BadMethodCallException("The DTO method \"$expectedMethod\" does not exist!", 1);
        }

        $select = $formatClassInst->{$expectedMethod}(...$args);
        $inst->raw = (method_exists($select, "get")) ? $select->get() : $select;
        return $inst;
    }

    /**
     * Get/return result
     *
     * @param  string|null $fallback
     * @return mixed
     */
    public function get(?string $fallback = null): mixed
    {
        return ($this->raw ?? $fallback);
    }

    /**
     * Will add item to object and method chain
     *
     * @param string $key  The object key name
     * @param mixed $value The object item value
     * @return self
     */
    public function add(string $key, mixed $value): self
    {
        $inst = clone $this;
        $inst->addToObject($key, $value);
        $inst->raw = $inst->getData()->{$key};
        return $inst;
    }

    /**
     * Validate the current item
     *
     * @example $this->email->validator()->isEmail() // returns bool
     * @return Validator
     * @throws ErrorException
     */
    public function validator(): Validator
    {
        return Validator::value($this->raw);
    }

    /**
     * Validate the current item and set to fallback (default: null) if not valid
     *
     * @param string $method
     * @param array $args
     * @return bool
     * @throws ErrorException|BadMethodCallException
     */
    public function valid(string $method, array $args = []): bool
    {
        $inp = Validator::value($this->raw);
        if (!method_exists($inp, $method)) {
            throw new BadMethodCallException("The MaplePHP validation method \"$method\" does not exist!", 1);
        }
        return $inp->{$method}(...$args);
    }

    /**
     * Returns the JSON representation of a value
     *
     * https://www.php.net/manual/en/function.json-encode.php
     *
     * @param int $flags
     * @param int $depth
     * @return string|false
     */
    public function toJson(int $flags = 0, int $depth = 512): string|false
    {
        return json_encode($this->get(), $flags, $depth);
    }

    /**
     * Returns the string representation of the value
     *
     * @return string
     */
    public function toString(): string
    {
        return (string)$this->get();
    }

    /**
     * Returns the int representation of the value
     *
     * @return int
     */
    public function toInt(): int
    {
        return (int)$this->get();
    }

    /**
     * Returns the float representation of the value
     *
     * @return float
     */
    public function toFloat(): float
    {
        return (float)$this->get();
    }

    /**
     * Returns the bool representation of the value
     *
     * @return bool
     */
    public function toBool(): bool
    {
        $value = $this->get();
        if (is_bool($value)) {
            return $value;
        }
        if (is_numeric($value)) {
            return ((float)$value > 0);
        }
        return ($value !== "false" && strlen($value));
    }

    /**
     * Convert a collection into an array
     *
     * @param callable|null $callback
     * @return array
     */
    public function toArray(?callable $callback = null): array
    {
        $index = 0;
        $new = [];
        $inst = clone $this;

        if ($inst->raw === null) {
            $inst->raw = $inst->getData();
        }

        if (!is_object($inst->raw) && !is_array($inst->raw)) {
            $inst->raw = [$inst->raw];
        }

        foreach ($inst->raw as $key => $row) {
            if (is_callable($callback) &&
                (($get = $callback($row, $key, $index)) !== false)) {
                $row = $get;
            }
            if ($row instanceof self) {
                $row = $row->get();
            }
            $new[$key] = $row;
            $index++;
        }
        return $new;
    }

    /**
     * Accepts and validates data types
     *
     * This method checks if the raw data type matches any of the valid data types provided.
     * If matched, returns the raw value, otherwise throws an exception.
     *
     * @template T of 'array'|'object'|'bool'|'int'|'float'|'string'|'resource'|'null'|'callable'|'closure'
     * @param array $validDataType List of valid data types to check against
     * @psalm-param list<T> $validDataType
     * @return mixed The raw value if type matches, otherwise throws exception
     * @throws BadMethodCallException If data type is not supported
     * @psalm-return (
     *     T is 'array'? array:
     *     T is 'object'? object:
     *     T is 'bool'? bool:
     *     T is 'int'? int:
     *     T is 'float'? float:
     *     T is 'string'? string:
     *     T is 'resource'? mixed:
     *     T is 'null'? null:
     *     T is 'callable'? callable:
     *     T is 'closure'? \Closure:
     *     mixed
     * )
     */
    public function acceptType(array $validDataType = []): mixed
    {
        if (is_array($this->raw) && in_array("array", $validDataType)) {
            return $this->raw;
        }
        if (is_object($this->raw) && in_array("object", $validDataType)) {
            return $this->raw;
        }
        if (is_bool($this->raw) && in_array("bool", $validDataType)) {
            return $this->raw;
        }
        if (is_int($this->raw) && in_array("int", $validDataType)) {
            return $this->raw;
        }
        if (is_float($this->raw) && in_array("float", $validDataType)) {
            return $this->raw;
        }
        if (is_string($this->raw) && in_array("string", $validDataType)) {
            return $this->raw;
        }
        if ($this->raw === null && in_array("null", $validDataType)) {
            return $this->raw;
        }
        if (is_callable($this->raw) && in_array("callable", $validDataType)) {
            return $this->raw;
        }
        if (($this->raw instanceof Closure) && in_array("closure", $validDataType)) {
            return $this->raw;
        }
        if (is_resource($this->raw) && in_array("resource", $validDataType)) {
            return $this->raw;
        }
        throw new BadMethodCallException("The DTO data type is not supported!", 1);
    }

    /**
     * Immutable: Access incremental array
     *
     * @param callable|null $callback Access array row in the callback argument
     * @return array|object|null
     */
    public function fetch(?callable $callback = null): array|object|null
    {
        $index = 0;
        $new = [];
        $inst = clone $this;

        if ($inst->raw === null) {
            $inst->raw = $inst->getData();
        }

        foreach ($inst->raw as $key => $row) {
            if ($callback !== null) {
                if (($get = $callback($inst::value($row), $key, $row, $index)) !== false) {
                    $new[$key] = $get;
                } else {
                    break;
                }

            } else {
                if (is_array($row) || ($row instanceof stdClass)) {
                    // Incremental -> object
                    $value = $inst::value($row);
                } elseif (is_object($row)) {
                    $value = $row;
                } else {
                    // Incremental -> value
                    $value = $row !== null ? Format\Str::value($row) : null;
                }
                $new[$key] = $value;
            }
            $index++;
        }

        $inst->raw = $new;
        return $inst->raw;
    }

    /**
     * Alias name to fetch
     *
     * @param callable $callback
     * @return array|object|null
     */
    public function each(callable $callback): array|object|null
    {
        return $this->fetch($callback);
    }

    /**
     * Dump a collection into a human-readable array dump
     *
     * @return void
     * @throws ReflectionException
     */
    public function dump(): void
    {
        Helpers::debugDump($this->toArray(), "Traverse");
    }

    /**
     * Count if the row is an array. Can be used to validate before @fetch method
     *
     * @return int
     */
    public function count(): int
    {
        return (is_array($this->raw) ? count($this->raw) : 0);
    }

    /**
     * Isset
     *
     * @return mixed
     */
    public function isset(): mixed
    {
        return (isset($this->raw)) ? $this->raw : false;
    }

    /**
     * Access and return format class object
     *
     * @param string $dtoClassName The DTO format class name
     * @param mixed $value
     * @return object
     * @throws ReflectionException|BadMethodCallException
     */
    protected function format(string $dtoClassName, mixed $value): object
    {
        $name = ucfirst($dtoClassName);
        $className = "MaplePHP\\DTO\\Format\\$name";

        if (!in_array($name, self::$helpers)) {
            throw new BadMethodCallException("The DTO class \"$dtoClassName\" is not a Helper class! " .
                "You can add helper class with 'addHelper' if you wish.", 1);
        }

        if (!class_exists($className) || !in_array($name, self::$helpers)) {
            throw new BadMethodCallException("The DTO class \"$dtoClassName\" does not exist!", 1);
        }

        $reflect = new ReflectionClass($className);
        $instance = $reflect->newInstanceWithoutConstructor();
        return $instance->value($value);
    }

    /**
     * Build the object
     *
     * @param mixed $data
     * @return $this
     */
    protected function build(mixed $data): self
    {
        if (is_array($data) || is_object($data)) {
            foreach ($data as $k => $v) {
                $this->{$k} = $v;
            }
        }

        $this->raw = $data;
        return $this;
    }

}
