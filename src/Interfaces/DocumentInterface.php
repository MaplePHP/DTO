<?php

namespace MaplePHP\DTO\Interfaces;

interface DocumentInterface
{
    /**
     * Will output get
     * @return string
     */
    public function __toString(): string;

    /**
     * Get get Dom/document (Will only trigger execute once per instance)
     * @return string
     */
    public function get(): string;

    /**
     * Init DOM instance
     * @param  string $key DOM access key
     * @return self
     */
    public static function dom(string $key): self;

    /**
     * Create and bind tag to a key so it can be overwritten
     * @param  string       $tag     HTML tag (without brackets)
     * @param  string       $key     Bind tag to key
     * @param  bool|boolean $prepend Prepend instead of append
     * @return ElementInterface
     */
    public function bindTag(string $tag, string $key, bool $prepend = false): ElementInterface;

    /**
     * Create (append) element
     * @param  string $element HTML tag (without brackets)
     * @param  string $value   add value to tag
     * @return ElementInterface
     */
    public function create($element, $value = null, ?string $bind = null): ElementInterface;

    /**
    * Prepend element first
    * @param  string $element HTML tag (without brackets)
    * @param  string $value   add value to tag
    * @return ElementInterface
    */
    public function createPrepend(string $element, ?string $value = null, ?string $bind = null): ElementInterface;

    /**
     * Get one element from key
     * @return ElementInterface|null
     */
    public function getElement(string $key): ?ElementInterface;

    /**
     * Get all elements
     * @return array
     */
    public function getElements(): array;

    /**
     * Get html tag
     * @param  string $key
     * @return string|null
     */
    public function getTag(string $key): ?string;

    /**
     * Execute and get Dom/document
     * @param  callable|null $call Can be used to manipulate element within feed
     * @return string
     */
    public function execute(?callable $call = null): string;
}
