<?php

namespace PHPFuse\DTO\Format;

final class Encode extends FormatAbstract implements FormatInterface
{
    //protected $value;
    protected $jsonEncode = true;
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
    public function urlEncode(bool $urlencode = true): self
    {
        $this->urlencode = $urlencode;
        return $this;
    }

    /**
     * Encode values
     * @param  callable|null $callback Access encode value with callable and build upon
     * @param  int           $flag ENT_QUOTES|ENT_SUBSTITUTE|ENT_HTML401
     * @return self
     */
    public function encode(?callable $callback = null, int $flag = ENT_QUOTES): self
    {
        if (is_array($this->value)) {
            $this->value = Arr::value($this->value)->walk(function ($value) use ($callback, $flag) {
                if (!is_null($callback)) {
                    $value = $callback($value);
                }
                $uri = Str::value((string)$value)->encode($flag);
                if ($this->urlencode) {
                    $uri->rawurlencode();
                }
                return $uri->get();
            })->get();
        } else {
            if (!is_null($callback)) {
                $this->value = $callback($this->value);
            }
            $this->value = Str::value($this->value)->encode($flag)->get();
        }

        return $this;
    }

    /**
     * XXS Protect the result
     * @return self
     */
    public function xss(?callable $callback = null): self
    {
        return $this->encode($callback);
    }
}
