<?php

namespace MaplePHP\DTO\Format;

final class Encode extends FormatAbstract implements FormatInterface
{
    //protected $value;
    protected bool $jsonEncode = true;
    protected bool $specialChar = true;
    protected int $specialCharFlag = ENT_NOQUOTES;
    protected bool $urlencode = false;
    protected bool $sanitizeIdentifiers = false;

    /**
     * Input is mixed data type in the interface because I do not know the type before
     * the Class constructor MUST handle the input validation
     * @param array|string $value
     */
    public function __construct(array|string $value)
    {
        parent::__construct($value);
    }

    /**
     * Init format by adding data to modify/format/traverse
     * @param  mixed  $value
     * @return self
     */
    public static function value(mixed $value): FormatInterface
    {
        return new self($value);
    }

    /**
     * Remove any character that is not a letter, number, underscore, or dash
     * Can be used to sanitize SQL identifiers that should be enclosed in backticks
     * @param  bool $sanitizeIdentifiers
     * @return self
     */
    public function sanitizeIdentifiers(bool $sanitizeIdentifiers): self
    {
        $this->sanitizeIdentifiers = $sanitizeIdentifiers;
        return $this;
    }

    /**
     * Url encode flag
     * @param bool $encode
     * @return self
     */
    public function urlEncode(bool $encode): self
    {
        $this->urlencode = $encode;
        return $this;
    }

    /**
     * Special Char encode
     * @param bool $encode
     * @param int $flag ENT_QUOTES|ENT_SUBSTITUTE|ENT_HTML401
     * @return self
     */
    public function specialChar(bool $encode, int $flag = ENT_NOQUOTES): self
    {
        $this->specialChar = $encode;
        $this->specialCharFlag = $flag;
        return $this;
    }

    /**
     * Encode values
     * @return string|array
     */
    public function encode(): string|array
    {
        // Always url decode first
        $this->value = $this->urldecode(function ($value) {
            $uri = Str::value((string)$value);
            if ($this->urlencode) {
                $uri->rawurlencode();
            }
            if ($this->sanitizeIdentifiers) {
                $uri->sanitizeIdentifiers();
            }
            if ($this->specialChar) {
                $uri->encode($this->specialCharFlag);
            }
            return $uri->get();
        });

        return $this->value;
    }

    /**
     * Url decode
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
     * @param callable|null $callback
     * @return array|string
     */
    public function xss(?callable $callback = null): array|string
    {
        return $this->specialChar(true)->encode();
    }
}
