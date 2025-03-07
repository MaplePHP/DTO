<?php

/**
 * @Package:    MaplePHP Format string
 * @Author:     Daniel Ronkainen
 * @Licence:    Apache-2.0 license, Copyright © Daniel Ronkainen
                Don't delete this comment, its part of the license.
 */

namespace MaplePHP\DTO\Format;

use MaplePHP\Output\Dom\Document;
use InvalidArgumentException;

final class Dom extends FormatAbstract
{
    //protected $value;
    protected $dom;
    protected $str;

    /**
     * Input is mixed data type in the interface becouse I do not know the type before the class     *  constructor MUST handle the input validation
     * @param string $value
     */
    public function __construct(mixed $value)
    {
        if (is_array($value) || is_object($value)) {
            throw new InvalidArgumentException("Is expecting a string or a convertable string value.", 1);
        }
        parent::__construct($value);
    }

    /**
     * Static access
     * @param  mixed  $value
     * @return self
     */
    public static function value(mixed $value): self
    {
        $inst = new static($value);
        $inst->dom = Document::dom("DTO");
        return $inst;
    }

    /**
     * Get DOM if is modified
     * E.G. Will create an interface for the Document and Element class
     * @return object
     */
    public function getDom(): object
    {
        return $this->dom;
    }

    /**
     * Create elemt with some default values
     * @param  string $tag
     * @return object
     */
    public function create(string $tag): object
    {
        if (is_array($this->value)) {
            $arr = $this->value;
            $this->value = (isset($arr['value'])) ? $arr['value'] : "";
            $this->str = Str::value($this->value);

            $attr = ($arr['attr'] ?? []);
            $elem = $this->dom->create($tag, $this->str)->hideEmptyTag(true);
            if (is_array($attr) && count($attr) > 0) {
                $elem->attrArr($attr);
            }
            return $elem;
        }

        $this->str = Str::value($this->value);
        return $this->dom->create($tag, $this->str)->hideEmptyTag(true);
    }
}
