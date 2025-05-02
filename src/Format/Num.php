<?php

/**
 * DEPRECATED
 * @Package:    MaplePHP Format numbers
 * @Author:     Daniel Ronkainen
 * @Licence:    Apache-2.0 license, Copyright © Daniel Ronkainen
                Don't delete this comment, its part of the license.
 * @Version:    1.0.0
 */

namespace MaplePHP\DTO\Format;

use NumberFormatter;

final class Num extends FormatAbstract implements FormatInterface
{
    private static NumberFormatter $defNumInst;

    private ?NumberFormatter $numInst = null;

    /**
     * Init format by adding data to modify/format/traverse
     *
     * @param  mixed  $value
     * @return self
     */
    public static function value(mixed $value): FormatInterface
    {
        return new static($value);
    }

    /**
     * Initiate a NumberFormatter instance
     *
     * @param string $locale
     * @param int $type
     * @return void
     */
    public static function setDefaultLocale(string $locale, int $type = NumberFormatter::CURRENCY): void
    {
        self::$defNumInst = new NumberFormatter($locale, $type);
    }

    /**
     * Initiate a NumberFormatter instance
     *
     * @param string $locale
     * @param int $type
     * @return self
     */
    public function setLocale(string $locale, int $type = NumberFormatter::CURRENCY): self
    {
        $inst = clone $this;
        $inst->numInst = new NumberFormatter($locale, $type);
        return $inst;
    }

    /**
     * Get expected NumberFormatter instance
     *
     * @return NumberFormatter
     */
    public function getNumFormatter(): NumberFormatter
    {
        if (!is_null($this->numInst)) {
            return $this->numInst;
        }
        if (is_null(self::$defNumInst)) {
            throw new \InvalidArgumentException("NumberFormatter instance not set.");
        }
        return self::$defNumInst;
    }

    /**
     * Convert to float number
     *
     * @return self
     */
    public function float(): self
    {
        $inst = clone $this;
        $inst->raw = (float)$inst->raw;
        return $inst;
    }

    /**
     * Convert to integer
     *
     * @return self
     */
    public function int(): self
    {
        $inst = clone $this;
        $inst->raw = (int)$inst->raw;
        return $inst;
    }

    /**
     * Round number
     *
     * @param int $precision
     * @param int $mode
     * @return self
     */
    public function round(int $precision = 0, int $mode = PHP_ROUND_HALF_UP): self
    {
        $inst = $this->float();
        $inst->raw = round($inst->raw, $precision, $mode);
        return $inst;
    }

    /**
     * Floor number
     *
     * @return self
     */
    public function floor(): self
    {
        $inst = $this->float();
        $inst->raw = floor($inst->raw);
        return $inst;
    }

    /**
     * Ceil number
     *
     * @return self
     */
    public function ceil(): self
    {
        $inst = $this->float();
        $inst->raw = ceil($inst->raw);
        return $inst;
    }

    /**
     * Absolute value
     *
     * @return self
     */
    public function abs(): self
    {
        $inst = $this->float();
        $inst->raw = abs($inst->raw);
        return $inst;
    }

    /**
     * Format number with thousands separator
     *
     * @param int $decimals
     * @param string $decimalSeparator
     * @param string $thousandsSeparator
     * @return self
     */
    public function numberFormat(int $decimals = 0, string $decimalSeparator = '.', string $thousandsSeparator = ','): self
    {
        $inst = $this->float();
        $inst->raw = number_format($inst->raw, $decimals, $decimalSeparator, $thousandsSeparator);
        return $inst;
    }

    /**
     * Pad number with leading zeros
     *
     * @param int $length
     * @return self
     */
    public function leadingZero(int $length = 2): self
    {
        $inst = clone $this;
        $inst->raw = Str::value($inst->raw)->leadingZero($length)->get();
        return $inst;
    }

    /**
     * Clamp number between min and max
     *
     * @param float $min
     * @param float $max
     * @return self
     */
    public function clamp(float $min, float $max): self
    {
        $inst = $this->float();
        $inst->raw = max($min, min($max, $inst->raw));
        return $inst;
    }

    /**
     * Check if number is even
     *
     * @return Num
     */
    public function isEven(): self
    {
        $inst = $this->int();
        $inst->raw = ($inst->raw % 2) === 0;
        return $inst;
    }

    /**
     * Check if number is even
     *
     * @return Num
     */
    public function isOdd(): self
    {
        $inst = $this->int();
        $inst->raw = ($inst->raw % 2) !== 0;
        return $inst;
    }

    /**
     * Convert percentage string to decimal (e.g. '45%' => 0.45)
     *
     * @return self
     */
    public function percentToDecimal(): self
    {
        $inst = $this->float();
        $inst->raw = $inst->raw / 100;
        return $inst;
    }

    /**
     * Convert decimal to percentage string (e.g. 0.45 => '45%')
     *
     * @param int $precision
     * @return self
     */
    public function toPercent(int $precision = 2): self
    {
        $inst = $this->float();
        $inst->raw = round($inst->raw * 100, $precision);
        return $inst;
    }

    /**
     * Get byte number or filesize in Kilobyte
     *
     * @return self
     */
    public function toKb(): self
    {
        $inst = $this->float();
        $inst->raw = round(($inst->raw / 1024), 2);
        return $inst;
    }

    /**
     * Formats the bytes to appropriate ending (kb, mb, g or t)
     *
     * @return self
     */
    public function toFilesize(): self
    {
        $inst = $this->float();
        $precision = 2;
        $base = log($inst->raw) / log(1024);
        $suffixes = ['', ' kb', ' mb', ' g', ' t'];
        $baseFloor = (int)floor($base);
        $suffix = (isset($suffixes[$baseFloor])) ? $suffixes[$baseFloor] : "";
        $inst->raw = round(pow(1024, $base - $baseFloor), $precision) . $suffix;
        return $inst;
    }

    /**
     * Number to bytes
     *
     * @return self
     */
    public function toBytes(): self
    {
        $inst = $this->float();
        $val = $inst->raw;

        preg_match('#([0-9]+)[\s]*([a-z]+)#i', $val, $matches);
        $last = isset($matches[2]) ? $matches[2] : "";
        if (isset($matches[1])) {
            $val = (int)$matches[1];
        }

        switch (strtolower($last)) {
            case 'g':
            case 'gb':
                $val *= 1024;
                // no break
            case 'm':
            case 'mb':
                $val *= 1024;
                // no break
            case 'k':
            case 'kb':
                $val *= 1024;
        }
        $inst->raw = $val;

        return $inst;
    }

    /**
     * Convert number to a localized currency string
     *
     * @param string $currency
     * @param int $decimals
     * @param int $roundingMode
     * @return self
     */
    public function toCurrency(
        string $currency,
        int $decimals = 2,
        int $roundingMode = NumberFormatter::ROUND_HALFUP
    ): self {
        $inst = $this->float();
        $num = $inst->getNumFormatter();
        $num->setAttribute(NumberFormatter::ROUNDING_MODE, $roundingMode);
        $num->setAttribute(NumberFormatter::FRACTION_DIGITS, $decimals);
        $inst->raw = $num->formatCurrency($inst->raw, $currency);
        return $inst;
    }

    /**
     * Get only the currency symbol (e.g. 'kr' for SEK)
     *
     * @param string $currency
     * @return self
     */
    public function getCurrencySymbol(string $currency): self
    {
        $inst = clone $this;
        $num = $this->getNumFormatter();
        $num->setTextAttribute(NumberFormatter::CURRENCY_CODE, $currency);
        $inst->raw = $num->getSymbol(NumberFormatter::CURRENCY_SYMBOL);
        return $inst;
    }

    /**
     * Format number with ISO currency prefix (e.g. SEK 1,000.00)
     *
     * @param string $currency
     * @param int $decimals
     * @return self
     */
    public function toCurrencyIso(string $currency, int $decimals = 2): self
    {
        $inst = $this->float();
        $num = $inst->getNumFormatter();
        $num->setAttribute(NumberFormatter::FRACTION_DIGITS, $decimals);
        $inst->raw = $currency . ' ' . $num->format($inst->raw);
        return $inst;
    }
}
