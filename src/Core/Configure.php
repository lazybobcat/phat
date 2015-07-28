<?php

namespace Phat\Core;

/**
 * The Configure class is a static container of the application configuration.
 */
class Configure
{
    /**
     * @var array Contains all the configurations key/value pairs
     */
    protected static $values = [
        'debug' => true,
    ];

    /**
     * Writes a key/value pair into the configuration array.
     *
     * @param string $entry The key
     * @param mixed  $value The value
     *
     * @return bool
     */
    public static function write($entry, $value = null)
    {
        if (!is_array($entry)) {
            $entry = [$entry => $value];
        }

        foreach ($entry as $name => $value) {
            self::$values[$name] = $value;
        }

        if (isset($entry['debug']) && function_exists('ini_set')) {
            ini_set('display_errors', $entry['debug'] ? 1 : 0);
        }

        return true;
    }

    /**
     * Returns the value of the given key.
     * Returns the whole configuration array if the key is null.
     * Returns an empty string if the key is not set in the configuration array.
     *
     * @param string $entry The key
     *
     * @return string The value
     */
    public static function read($entry = null)
    {
        if(null === $entry) {
            return self::$values;
        }

        if (self::check($entry)) {
            return self::$values[$entry];
        }

        return '';
    }

    /**
     * Checks if a key exists in the configuration array.
     *
     * @param string $entry The key
     *
     * @return bool
     */
    public static function check($entry)
    {
        return array_key_exists($entry, self::$values);
    }

    /**
     * Removes a key/value pair from th configuration array given the key.
     *
     * @param string $entry The key
     */
    public static function erase($entry)
    {
        unset(self::$values[$entry]);
    }

    /**
     * Clear the configuration array completely.
     */
    public static function clear()
    {
        self::$values = [];
    }
}
