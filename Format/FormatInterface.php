<?php

/**
 * @Package:    PHPFuse Format Abstraction Class
 * @Author:     Daniel Ronkainen
 * @Licence:    The MIT License (MIT), Copyright © Daniel Ronkainen
                Don't delete this comment, its part of the license.
 * @Version:    1.0.0
 */

namespace PHPFuse\DTO\Format;

interface FormatInterface
{
    /**
     * Init format by adding data to modify/format/traverse
     * @param  mixed $value
     * @return self
     */
    public static function value(mixed $value);

    public function __toString();

    //public function get():mixed;
}
