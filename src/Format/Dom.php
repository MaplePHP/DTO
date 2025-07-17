<?php

/**
 * @Package:    MaplePHP Format string
 * @Author:     Daniel Ronkainen
 * @Licence:    Apache-2.0 license, Copyright © Daniel Ronkainen
                Don't delete this comment, its part of the license.
 */

namespace MaplePHP\DTO\Format;

use InvalidArgumentException;
use MaplePHP\DTO\Dom\Document;
use MaplePHP\DTO\Interfaces\ElementInterface;

final class Dom extends FormatAbstract
{
    //protected $value;
    protected Document $dom;
    protected Str $str;

    protected string $tag = "div";
    protected array $attr = [];

    /**
     * Input is mixed data type in the interface because I do not know the type before the class
     * constructor MUST handle the input validation
     *
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
     *
     * @param mixed $value
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
     *
     * @return Document
     */
    public function document(): Document
    {
        return $this->dom;
    }


    /**
     * Create an HTML tag
     * if tag is prefix with h1.title it will add title as class attribute
     * if tag is prefix with h1#title it will add title as id attribute
     *
     * @param string $tag Add HTML tag name without brackets "<>"
     * @param array $attr Add multiple custom attributes to tag
     * @return $this
     */
    public function tag(string $tag, array $attr = []): self
    {
        $inst = clone $this;
        if ($attr) {
            $inst = $inst->attr($attr);
        }
        $inst->tag = $tag;
        if (Str::value($tag)->contains(".")->get()) {
            $exp = explode(".", $tag, 2);
            $inst = $inst->class($exp[1]);
            $inst->tag = $exp[0];
            return $inst;
        }
        if (Str::value($tag)->contains("#")->get()) {
            $exp = explode("#", $tag, 2);
            $inst = $inst->id($exp[1]);
            $inst->tag = $exp[0];
            return $inst;
        }
        return $inst;
    }

    /**
     * Set class name
     *
     * @param string $className
     * @return $this
     */
    public function class(string $className): self
    {
        $inst = clone $this;
        $inst->attr['class'] = $className;
        return $inst;
    }

    /**
     * Set class name
     *
     * @param string $idName
     * @return $this
     */
    public function id(string $idName): self
    {
        $inst = clone $this;
        $inst->attr['id'] = $idName;
        return $inst;
    }

    /**
     * Add attributes to html tag
     *
     * @param array $attr
     * @return $this
     */
    public function attr(array $attr): self
    {
        $inst = clone $this;
        $inst->attr = $attr;
        return $inst;
    }

    /**
     * Build an element inside a callable
     *
     * @param callable $call
     * @return self
     */
    public function build(callable $call): self
    {
        $inst = clone $this;
        return $call($inst);
    }

    /**
     * Get element as ElementInterface
     *
     * @return ElementInterface
     */
    public function element(): ElementInterface
    {
        $this->str = Str::value($this->raw);
        $elem = $this->dom->create($this->tag, $this->str)->hideEmptyTag(true);
        if ($this->attr) {
            $elem->attrArr($this->attr);
        }
        return $elem;
    }

    /**
     * Get element as string
     *
     * @return string
     */
    public function get(): string
    {
        return $this->element();
    }
}
