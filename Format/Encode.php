<?php

namespace MaplePHP\DTO\Format;

final class Encode extends FormatAbstract implements FormatInterface
{
    //protected $value;
    protected $jsonEncode = true;
    protected $specialChar = true;
    protected $specialCharFlag = ENT_NOQUOTES;
    protected $urlencode = false;

    /**
     * Input is mixed data type in the interface becouse I do not know the type before the class
     * The class constructor MUST handle the input validation
     * @param array|string $value
     */
    public function __construct(array|string $value)
    {
        $this->value = $value;
    }

    /**
     * Init format by adding data to modify/format/traverse
     * @param  mixed  $value
     * @return self
     */
    public static function value(mixed $value): FormatInterface
    {
        $inst = new static($value);
        return $inst;
    }

    /**
     * Url encode flag
     * @param  bool $urlencode
     * @return self
     */
    public function urlEncode(bool $encode): self
    {
        $this->urlencode = $encode;
        return $this;
    }

    /**
     * Special Char encode
     * @param  bool $urlencode
     * @param  int  $flag ENT_QUOTES|ENT_SUBSTITUTE|ENT_HTML401
     * @return self
     */
    public function specialChar(bool $encode, $flag = ENT_NOQUOTES): self
    {
        $this->specialChar = $encode;
        $this->specialCharFlag = $flag;
        return $this;
    }

    /**
     * Encode values
     * @param  callable|null $callback Access encode value with callable and build upon
     * @return string|array
     */
    public function encode(?callable $callback = null): string|array
    {
        // Allways url decode first
        $this->value = $this->urldecode(function($value) {
            $uri = Str::value((string)$value);
            if ($this->urlencode) {
                $uri->rawurlencode();
            }
            if ($this->specialChar) {
                $uri->encode($this->specialCharFlag);
            }
            return $uri->get();
        });

        return $this->value;
    }

    /**
     * urldecode
     * @param  callable|null $callback Access encode value with callable and build upon
     * @return string|array
     */
    public function urldecode(?callable $callback = null): string|array
    {
        if (is_array($this->value)) {

            $this->value = Arr::value($this->value)->walk(function ($value) use ($callback) {
                $value = Str::value((string)$value)->rawurldecode()->get();
                if (!is_null($callback)) {
                    $value = $callback($value);
                }
                return $value;

            })->get();
        } else {
            $this->value = Str::value($this->value)->rawurldecode()->get();
            if (!is_null($callback)) {
                $this->value = $callback($this->value);
            }
        }
        return $this->value;
    }

    /**
     * XXS Protect the result
     * @return self
     */
    public function xss(?callable $callback = null): self
    {
        return $this->specialChar(true)->encode($callback);
    }
}
