<?php

/**
 * @Package:    PHPFuse Format string
 * @Author:     Daniel Ronkainen
 * @Licence:    The MIT License (MIT), Copyright © Daniel Ronkainen
                Don't delete this comment, its part of the license.
 */

namespace PHPFuse\DTO\Format;

final class Str extends FormatAbstract implements FormatInterface
{
    protected $value;


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


    /**
     * Gte value as string
     * @return string
     */
    public function strVal(): string
    {
        return (string)$this->value;
    }

    /**
     * Excerpt/shorten down text/string
     * @param  integer $length total length
     * @param  string  $ending When break text add a ending (...)
     * @return string
     */
    public function excerpt($length = 40, $ending = "..."): self
    {
        $this->stripTags()->entityDecode();
        $this->value = str_replace(array("'", '"', "”"), array("", "", ""), $this->value);
        $strlen = strlen($this->value);
        $this->value = trim(mb_substr($this->value, 0, $length));
        if ($strlen > $length) {
            $this->value .= $ending;
        }
        return $this;
    }

    /**
     * Convert new line to html <br>
     * @return [type] [description]
     */
    public function nl2br(): self
    {
        $this->value = nl2br($this->strVal());
        return $this;
    }

    /**
     * Make sure string allways end with a trailing slash (will only add slash if it does not exist)
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
     * @return self
     */
    public function specialchars(): self
    {
        $this->value = htmlspecialchars($this->strVal(), ENT_QUOTES, 'UTF-8');
        return $this;
    }

    /**
     * Cleans GET/POST data (XSS protection)
     * @return self
     */
    public function encode(): self
    {
        $this->specialchars();
        return $this;
    }

    /**
     * Decode html special characters
     * @return self
     */
    public function decode(): self
    {
        $this->value = htmlspecialchars_decode($this->strVal(), ENT_QUOTES);
        return $this;
    }

    /**
     * Clears soft breaks
     * @return self
     */
    public function clearBreaks(): self
    {
        $this->value = preg_replace('/(\v|\s)+/', ' ', $this->strVal());
        return $this;
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
     * strtolower
     * @return self
     */
    public function toLower(): self
    {
        $this->value = strtolower($this->strVal());
        return $this;
    }

    /**
     * strtoupper
     * @return self
     */
    public function toUpper(): self
    {
        $this->value = strtoupper($this->strVal());
        return $this;
    }

    /**
     * ucfirst
     * @return self
     */
    public function ucfirst(): self
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
        $this->clearBreaks("-")->trim()->replaceSpecialChar()->trimSpaces()->replaceSpaces("-")->tolower();
        $this->value = preg_replace("/[^a-z0-9\s-]/", "", $this->value);
        return $this;
    }

    /**
     * Replaces special characters to it's counter part to "A" or "O"
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
    public function urldecode(): self
    {
        $this->value = urldecode($this->strVal());
        return $this;
    }

    /**
     * Url encode (with string replace if you want)
     * @param  array $find     Search values
     * @param  array $replace  Replace values
     * @return self
     */
    public function urlencode(?array $find = null, ?array $replace = null): self
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
    public function rawurldecode(): self
    {
        $this->value = rawurldecode($this->strVal());
        return $this;
    }

    /**
     * Raw url encode (with string replace if you want)
     * @param  array $find     Search values
     * @param  array $replace  Replace values
     * @return self
     */
    public function rawurlencode(?array $find = null, ?array $replace = null): self
    {
        $this->value = rawurlencode($this->strVal());
        if (!is_null($find) && !is_null($replace)) {
            $this->replace($find, $replace);
        }
        return $this;
    }

    /**
     * String replace
     * @param  array $find     Search values
     * @param  array $replace  Replace values
     * @return self
     */
    public function replace($find, $replace): self
    {
        $this->value = str_replace($find, $replace, $this->strVal());
        return $this;
    }

    /**
     * Decode then encode url (with string replace if you want)
     * @param  array $find     Search values
     * @param  array $replace  Replace values
     * @return self
     */
    public function toggleUrlencode(?array $find = null, ?array $replace = null): self
    {
        return $this->urldecode()->rawurlencode($find, $replace);
    }



    /**
     * Explode return array instance
     * @return Arr
     */
    public function explode(string $delimiter): Arr
    {
        return Arr::value(explode($delimiter, $this->value));
    }

    /**
     * Will convert all camlecase words to array and return array instance
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
}
