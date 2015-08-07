<?php

namespace Phat\Utils;


class Inflector
{


    public static function underscore($string)
    {
        $string = str_replace('-', '_', $string);
        $string = mb_strtolower(preg_replace('/(?<=\\w)([A-Z])/', '_' . '\\1', $string));

        return $string;
    }
}