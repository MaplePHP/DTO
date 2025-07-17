<?php

namespace MaplePHP\DTO\Interfaces;

interface ElementInterface
{
    /**
     * Overwrite the current element
     * @param string $elem HTML Tag name
     */
    public function setElement(string $elem): self;

    /**
     * Set html attribute
     * @param  string      $key attr key
     * @param  string|null $val attr value
     * @return self
     */
    public function attr(string $key, ?string $val = null): self;

    /**
     * Set multiple html attributes
     * @param  array    [key => value]
     * @return self
     */
    public function attrArr(?array $arr): self;

    /**
     * Hide html tag if its value is empty
     * @param  bool   $bool
     * @return self
     */
    public function hideEmptyTag(bool $bool): self;

    /**
     * Add value to attr
     * @param  string $key
     * @param  string $value
     * @param  string $sep
     * @return self
     */
    public function addAttr(string $key, string $value, string $sep = " "): self;

    /**
     * Set elem value <elem>[VALUE]</elem>
     * @param string|null null value can be used to auto skip HTML tag
     * @return self
     */
    public function setValue(?string $value): self;

    /**
     * Set elem value
     * @return string
     */
    public function getValue(): string;

    /**
     * Get elem/HTML tag
     * @return string
     */
    public function getEl(): string;

    /**
     * With cloned element or new element if is specifed
     * @param  string|null $elem
     * @return self
     */
    public function withElement(?string $elem = null): self;
}
