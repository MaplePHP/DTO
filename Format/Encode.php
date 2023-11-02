<?php


namespace PHPFuse\DTO\Format;

class Encode extends formatAbstract implements FormatInterface {

   protected $value;
   protected $jsonEncode = true;
   protected $urlencode = false;


    function urlEncode(bool $urlencode = true): self
    {
        $this->urlencode = $urlencode;
        return $this;
    }

    /**
     * XXS Protect the result
     * @return self
     */
    function encode(?callable $callback = NULL): self
    {
        if(is_array($this->value)) {
            $this->value = Arr::value($this->value)->walk(function($value) use($callback) {
                if(!is_null($callback)) $value = $callback($value);
                $uri = Str::value((string)$value)->encode();
                if($this->urlencode) $uri->rawurlencode();
                return $uri->get();
            })->get();

        } else {
            if(!is_null($callback)) $this->value = $callback($this->value);
            $this->value = Str::value($this->value)->encode()->get();
        }

        return $this;
    }

}
