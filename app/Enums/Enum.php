<?php


namespace App\Enums;


abstract class Enum
{
    protected static $values;

    public static function getValues() {
        return static::$values;
    }

    public static function isValidValue($value) {
        return in_array($value, static::$values);
    }
}
