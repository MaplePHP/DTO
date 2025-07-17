<?php

/**
 * @Package:    MaplePHP - DOM Element class
 * @Author:     Daniel Ronkainen
 * @Licence:    Apache-2.0 license, Copyright © Daniel Ronkainen
                Don't delete this comment, its part of the license.
 * @Version:    1.0.0
 */

namespace MaplePHP\DTO\Dom;

use MaplePHP\DTO\Interfaces\ElementInterface;

class Element extends Document implements ElementInterface
{
    private $elem;
    private array $attr = [];
    private bool $snippet;
    private $value;
    private $hideEmptyTag = false;

    public function __construct(string $elem, ?string $value, bool $snippet = false)
    {
        $this->elem = $elem;
        $this->value = $value;
        $this->snippet = $snippet;
    }

    /**
     * Overwrite the current element
     * @param string $elem HTML Tag name
     */
    public function setElement(string $elem): self
    {
        $this->elem = $elem;
        return $this;
    }

    /**
     * Set html attribute
     * @param  string      $key attr key
     * @param  string|null $val attr value
     * @return self
     */
    public function attr(string $key, ?string $val = null): self
    {
        $this->attr[$key] = $val;
        return $this;
    }

    /**
     * Set multiple html attributes
     * @param  array    [key => value]
     * @return self
     */
    public function attrArr(?array $arr): self
    {
        if (is_array($arr)) {
            $this->attr = array_merge($this->attr, $arr);
        }
        return $this;
    }

    /**
     * Hide html tag if its value is empty
     * @param  bool   $bool
     * @return self
     */
    public function hideEmptyTag(bool $bool): self
    {
        $this->hideEmptyTag = $bool;
        return $this;
    }

    /**
     * Validate hide tag value
     * @return bool
     */
    protected function hideTagValid(): bool
    {
        return (($this->hideEmptyTag && !$this->value));
    }

    /**
     * Add value to attr
     * @param  string $key
     * @param  string $value
     * @param  string $sep
     * @return self
     */
    public function addAttr(string $key, string $value, string $sep = " "): self
    {
        if (isset($this->attr[$key])) {
            $this->attr[$key] .= "{$sep}{$value}";
        } else {
            $this->attr[$key] = $value;
        }
        return $this;
    }

    /**
     * Set elem value <elem>[VALUE]</elem>
     * @param string|null null value can be used to auto skip HTML tag
     * @return self
     */
    public function setValue(?string $value): self
    {
        $this->value = $value;
        return $this;
    }

    /**
     * Set elem value
     * @return string
     */
    public function getValue(): string
    {
        return (string)$this->value;
    }

    /**
     * Get elem/HTML tag
     * @return string
     */
    public function getEl(): string
    {
        return $this->elem;
    }

    /**
     * With cloned element or new element if is specifed
     * @param  string|null $elem
     * @return self
     */
    public function withElement(?string $elem = null): self
    {
        $inst = clone $this;
        if ($elem !== null) {
            $inst->elem = $elem;
        }
        return $inst;
    }

    /**
     * Array attr to string
     * @return string
     */
    protected function buildAttr(): string
    {
        $attr = "";
        if (count($this->attr) > 0) {
            foreach ($this->attr as $k => $v) {
                $attr .= " {$k}";
                if ($v !== null) {
                    $attr .= "=\"{$v}\"";
                }
            }
        }
        return $attr;
    }
}
