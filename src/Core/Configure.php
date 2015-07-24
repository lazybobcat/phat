<?php

namespace Phat\Core;


class Configure {

    protected static $values = [
        'debug' => true,
    ];


    public static function write($entry, $value = null) {
        if(!is_array($entry)) {
            $entry = [$entry => $value];
        }

        foreach($entry as $name => $value) {
            self::$values[$name] = $value;
        }

        if(isset($entry['debug']) && function_exists('ini_set')) {
            ini_set('display_errors', $entry['debug'] ? 1 : 0);
        }

        return true;
    }


    public static function read($entry) {
        if(self::check($entry)) {
            return self::$values[$entry];
        }

        return null;
    }


    public static function check($entry) {
        return array_key_exists($entry, self::$values);
    }


    public static function erase($entry) {
        unset(self::$values[$entry]);
    }


    public static function clear() {
        self::$values = [];
    }

}