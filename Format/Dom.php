<?php

/**
 * @Package:    PHPFuse Format string
 * @Author:     Daniel Ronkainen
 * @Licence:    The MIT License (MIT), Copyright © Daniel Ronkainen
                Don't delete this comment, its part of the license.
 */

namespace PHPFuse\DTO\Format;

use PHPFuse\Output\Dom\Document;
use PHPFuse\DTO\Format\FormatInterface;

final class Dom extends FormatAbstract
{
    protected $value;
    protected $dom;
    protected $str;

    public static function value($value)
    {
        $inst = new static($value);
        $inst->dom = Document::dom("DTO");
        return $inst;
    }


    public function create(string $tag)
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
