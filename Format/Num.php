<?php

/**
 * @Package:    PHPFuse Format numbers
 * @Author:     Daniel Ronkainen
 * @Licence:    The MIT License (MIT), Copyright © Daniel Ronkainen
                Don't delete this comment, its part of the license.
 * @Version:    1.0.0
 */

namespace PHPFuse\DTO\Format;

final class Num extends FormatAbstract implements FormatInterface
{
    private static $numFormatter;


    /**
     * Init format by adding data to modify/format/traverse
     * @param  array  $arr
     * @return self
     */
    public static function value($value): FormatInterface
    {
        $inst = new static($value);
        return $inst;
    }

    public static function numFormatter()
    {
        if (is_null(self::$numFormatter)) {
            self::$numFormatter = new \NumberFormatter("sv_SE", \NumberFormatter::CURRENCY);
        }
        return self::$numFormatter;
    }

    /**
     * Convert to float number
     * @return float
     */
    public function float()
    {
        $this->value = (float)$this->value;
        return $this;
    }

    /**
     * Convert to integer
     * @return int
     */
    public function int()
    {
        $this->value = (int)$this->value;
        return $this;
    }

    /**
     * Round number
     * @param  int    $dec Set decimals
     * @return float
     */
    public function round(int $dec = 0)
    {
        $this->value = round($this->float()->get(), $dec);
        return $this;
    }

    /**
     * Floor float
     * @return int
     */
    public function floor()
    {
        $this->value = floor($this->float()->get());
        return $this;
    }

    /**
     * Ceil float
     * @return int
     */
    public function ceil()
    {
        $this->value = ceil($this->float()->get());
        return $this;
    }

    /**
     * Get file size in KB
     * @return slef
     */
    public function toKb()
    {
        $this->value = round(($this->float()->get() / 1024), 2);
        return $this;
    }

    /**
     * Formats the bytes to appropiate ending (k,M,G,T)
     * @param  float  $size bytesum
     * @param  integer $precision float precision (decimal count)
     * @return float
     */
    public function toFilesize()
    {
        $precision = 2;
        $base = log($this->float()->get()) / log(1024);
        $suffixes = array('', ' kb', ' mb', ' g', ' t');
        $this->value = round(pow(1024, $base - floor($base)), $precision) . $suffixes[floor($base)];
        return $this;
    }

    /**
     * Number to bytes
     * @return self
     */
    public function toBytes(): self
    {
        $val = $this->value;

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
        $this->value = $val;

        return $this;
    }

    /**
     * Convert number to a currence (e.g. 1000 = 1.000,00 kr)
     * @param  string      $currency SEK, EUR
     * @param  int|integer $decimals
     * @return FormatInterface::Str
     */
    public function currency(string $currency, int $decimals = 2): FormatInterface
    {
        self::numFormatter()->setAttribute(self::$numFormatter::ROUNDING_MODE, $decimals);
        self::numFormatter()->setAttribute(self::$numFormatter::FRACTION_DIGITS, $decimals);

        // Traverse back to string
        return Str::value(self::numFormatter()->formatCurrency($this->float()->get(), $currency));
    }
}
