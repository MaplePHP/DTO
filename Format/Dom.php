<?php
/**
 * @Package:    PHPFuse Format string
 * @Author:     Daniel Ronkainen
 * @Licence:    The MIT License (MIT), Copyright © Daniel Ronkainen
                Don't delete this comment, its part of the license.
 */

namespace PHPFuse\DTO\Format;

use PHPFuse\Output\Dom\Document;

class Dom extends Str
{
    protected $value;
    protected $dom;

    public static function value($value): FormatInterface
    {
        $inst = new static();
        $inst->value = $value;
        $inst->dom = Document::dom("DTO");
        return $inst;
    }


    public function create(string $tag)
    {
        if (is_array($this->value)) {
            $arr = $this->value;
            $this->value = (isset($arr['value'])) ? $arr['value'] : "";
            $attr = ($arr['attr'] ?? []);
            $el = $this->dom->create($tag, $this->value)->hideEmptyTag(true);
            if (is_array($attr) && count($attr) > 0) {
                $el->attrArr($attr);
            }
            return $el;
        }

        return $this->dom->create($tag, $this->strVal())->hideEmptyTag(true);
    }
}
