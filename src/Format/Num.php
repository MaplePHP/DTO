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
    private static $numFormatter;


    /**
     * Init format by adding data to modify/format/traverse
     * @param  mixed  $value
     * @return self
     */
    public static function value(mixed $value): FormatInterface
    {
        return new static($value);
    }

    /**
     * Add number format for currency
     * @return NumberFormatter
     */
    public static function numFormatter(): NumberFormatter
    {
        if (is_null(self::$numFormatter)) {
            self::$numFormatter = new NumberFormatter("sv_SE", NumberFormatter::CURRENCY);
        }
        return self::$numFormatter;
    }

    /**
     * Convert to float number
     * @return self
     */
    public function float(): self
    {
        $this->raw = (float)$this->raw;
        return $this;
    }

    /**
     * Convert to integer
     * @return self
     */
    public function int(): self
    {
        $this->raw = (int)$this->raw;
        return $this;
    }

    /**
     * Round number
     * @param  int    $dec Set decimals
     * @return self
     */
    public function round(int $dec = 0): self
    {
        $this->float();
        $this->raw = round($this->raw, $dec);
        return $this;
    }

    /**
     * Floor float
     * @return self
     */
    public function floor(): self
    {
        $this->float();
        $this->raw = floor($this->raw);
        return $this;
    }

    /**
     * Ceil float
     * @return self
     */
    public function ceil(): self
    {
        $this->float();
        $this->raw = ceil($this->raw);
        return $this;
    }

    /**
     * Get file size in KB
     * @return self
     */
    public function toKb(): self
    {
        $this->float();
        $this->raw = round(($this->raw / 1024), 2);
        return $this;
    }

    /**
     * Formats the bytes to appropiate ending (k,M,G,T)
     * @return self
     */
    public function toFilesize(): self
    {
        $this->float();
        $precision = 2;
        $base = log($this->raw) / log(1024);
        $suffixes = ['', ' kb', ' mb', ' g', ' t'];
        $baseFloor = (int)floor($base);
        $suffix = (isset($suffixes[$baseFloor])) ? $suffixes[$baseFloor] : "";
        $this->raw = round(pow(1024, $base - $baseFloor), $precision) . $suffix;
        return $this;
    }

    /**
     * Number to bytes
     * @return self
     */
    public function toBytes(): self
    {
        $val = $this->raw;

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
        $this->raw = $val;

        return $this;
    }

    /**
     * Convert number to a currency (e.g. 1000 = 1.000,00 kr)
     * @param  string      $currency SEK, EUR
     * @param  int|integer $decimals
     * @return FormatInterface
     */
    public function currency(string $currency, int $decimals = 2): FormatInterface
    {
        self::numFormatter()->setAttribute(self::$numFormatter::ROUNDING_MODE, $decimals);
        self::numFormatter()->setAttribute(self::$numFormatter::FRACTION_DIGITS, $decimals);
        // Traverse back to string
        return Str::value(self::numFormatter()->formatCurrency($this->float()->get(), $currency));
    }
}
