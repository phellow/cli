<?php

/**
 *
 */
class InputFake
{
    public static $inputs = array();

    /**
     *
     */
    public static function getInput()
    {
        return array_shift(self::$inputs);
    }
}