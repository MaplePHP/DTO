<?php

namespace MaplePHP\DTO;

use ReflectionClass;

class Helpers {

    /**
     * @throws \ReflectionException
     */
    static function debugDump($var, $label = null): void
    {
        if (is_object($var)) {
            $reflection = new ReflectionClass($var);
            $className = $reflection->getShortName();
            echo "$className {\n";
            foreach ($reflection->getProperties() as $property) {
                //$property->setAccessible(true);
                $propName = $property->getName();
                $propValue = $property->getValue($var);
                echo "    #$propName: ";
                self::printFormattedValue($propValue, 2);
            }
            echo "}\n";
        } elseif (is_array($var)) {
            echo "$label {\n";
            self::printFormattedValue($var, 1);
            echo "}\n";
        } else {
            var_dump($var);
        }
    }

    static function printFormattedValue($value, $indent = 0): void
    {
        $spacingS = $spacingA = str_repeat("    ", $indent);
        $spacingB = str_repeat("    ", $indent+1);
        if($indent > 1) {
            $spacingS = "";
        }
        if (is_array($value)) {
            echo "{$spacingS}array:" . count($value) . " [\n";
            foreach ($value as $key => $val) {
                echo "{$spacingB}{$key} => ";
                self::printFormattedValue($val, $indent + 1);
            }
            echo "{$spacingA}]\n";

        } elseif (is_object($value)) {
            self::debugDump($value);
        } else {
            echo var_export($value, true) . "\n";
        }
    }

    /**
     * Traverse Array from string
     *
     * @param array $array
     * @param string $key
     * @return array|bool
     */
    static function traversArrFromStr(array $array, string $key): mixed
    {
        $new = $array;
        $exp = explode(".", $key);
        foreach ($exp as $index) {
            if(!isset($new[$index])) {
                $new = false;
                break;
            }
            $new = $new[$index];
        }
        return $new;
    }

}

