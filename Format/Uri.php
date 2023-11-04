<?php
/**
 * @Package:    PHPFuse Format URI strings
 * @Author:     Daniel Ronkainen
 * @Licence:    The MIT License (MIT), Copyright © Daniel Ronkainen
                Don't delete this comment, its part of the license.
 * @Version:    1.0.0
 */

namespace PHPFuse\DTO\Format;

class Uri extends Str implements FormatInterface
{
    protected $value;


    /**
     * Extract path from URL
     * @return self
     */
    public function extractPath(): self
    {
        $this->value = (string)parse_url($this->value, PHP_URL_PATH);
        return $this;
    }

    /**
     * Get only dirname from path
     * @return self
     */
    public function dirname(): self
    {
        $this->value = dirname($this->value);
        return $this;
    }

    /**
     * Trim tailing slash
     * @return self
     */
    public function trimTrailingSlash(): self
    {
        $this->value = ltrim($this->value, '/');
        return $this;
    }

    /**
     * XXS protection
     * @param  string $str
     * @return self
     */
    public function xxs(): self
    {
        if (is_null($this->value)) {
            $this->value = null;
        } else {
            $this->value = Str::value($this->value)->specialchars()->get();
        }
        return $this;
    }
}
