<?php

namespace Phat\Utils;


class Inflector
{
    private static $singular = [
        '/(quiz)zes$/i'                                                    => '\1',
        '/(matr)ices$/i'                                                   => '\1ix',
        '/(vert|ind)ices$/i'                                               => '\1ex',
        '/^(ox)en/i'                                                       => '\1',
        '/(alias|status)es$/i'                                             => '\1',
        '/([octop|vir])i$/i'                                               => '\1us',
        '/(cris|ax|test)es$/i'                                             => '\1is',
        '/(shoe)s$/i'                                                      => '\1',
        '/(o)es$/i'                                                        => '\1',
        '/(bus)es$/i'                                                      => '\1',
        '/([m|l])ice$/i'                                                   => '\1ouse',
        '/(x|ch|ss|sh)es$/i'                                               => '\1',
        '/(m)ovies$/i'                                                     => '\1ovie',
        '/(s)eries$/i'                                                     => '\1eries',
        '/([^aeiouy]|qu)ies$/i'                                            => '\1y',
        '/([lr])ves$/i'                                                    => '\1f',
        '/(tive)s$/i'                                                      => '\1',
        '/(hive)s$/i'                                                      => '\1',
        '/([^f])ves$/i'                                                    => '\1fe',
        '/(^analy)ses$/i'                                                  => '\1sis',
        '/((a)naly|(b)a|(d)iagno|(p)arenthe|(p)rogno|(s)ynop|(t)he)ses$/i' => '\1\2sis',
        '/([ti])a$/i'                                                      => '\1um',
        '/(n)ews$/i'                                                       => '\1ews',
        '/s$/i'                                                            => '',
    ];

    private static $uncountable = ['equipment', 'information', 'rice', 'money', 'species', 'series', 'fish', 'sheep'];

    private static $irregular = [
        'person' => 'people',
        'man'    => 'men',
        'child'  => 'children',
        'sex'    => 'sexes',
        'move'   => 'moves'
    ];

    public static function singularize($word)
    {
        $lowercased_word = strtolower($word);
        foreach(self::$uncountable as $_uncountable){
            if(substr($lowercased_word, (-1 * strlen($_uncountable))) == $_uncountable){
                return $word;
            }
        }

        foreach(self::$irregular as $_plural => $_singular){
            if(preg_match('/(' . $_singular . ')$/i', $word, $arr)){
                return preg_replace('/(' . $_singular . ')$/i', substr($arr[0], 0, 1) . substr($_plural, 1), $word);
            }
        }

        foreach(self::$singular as $rule => $replacement){
            if(preg_match($rule, $word)){
                return preg_replace($rule, $replacement, $word);
            }
        }

        return $word;
    }

    public static function pluralize($word){
        $lowercased_word = strtolower($word);
        foreach(self::$uncountable as $_uncountable){
            if(substr($lowercased_word, (-1 * strlen($_uncountable))) == $_uncountable){
                return $word;
            }
        }

        foreach(self::$irregular as $_plural => $_singular){
            if(preg_match('/(' . $_plural . ')$/i', $word, $arr)){
                return preg_replace('/(' . $_plural . ')$/i', substr($arr[0], 0, 1) . substr($_singular, 1), $word);
            }
        }

        foreach(self::$plural as $rule => $replacement){
            if(preg_match($rule, $word)){
                return preg_replace($rule, $replacement, $word);
            }
        }
        return false;
    }

    public static function underscore($string)
    {
        $string = str_replace('-', '_', $string);
        $string = mb_strtolower(preg_replace('/(?<=\\w)([A-Z])/', '_' . '\\1', $string));

        return $string;
    }
}