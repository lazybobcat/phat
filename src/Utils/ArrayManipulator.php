<?php

namespace Phat\Utils;

use Phat\Core\Exception\InvalidArgumentException;

/**
 * Useful package of functions to manipulate arrays.
 */
class ArrayManipulator
{
    /**
     * Returns the value specified by the $path out of the array $data. You can use a dot separated path.
     * If no data is found, the $default value will be returned.
     *
     * @param array        $data    The data to look into
     * @param string|array $path    The path to look at
     * @param mixed        $default Default returned value if no data is found
     *
     * @return mixed The value
     *
     * @throws InvalidArgumentException
     */
    public static function get(array $data, $path, $default = null)
    {
        if (empty($data) || empty($path)) {
            return $default;
        }

        // Extract the parts
        if (is_string($path) || is_numeric($path)) {
            $parts = explode('.', $path);
        } else {
            if (!is_array($path)) {
                throw new InvalidArgumentException(sprintf(
                    "Invalid path parameter of type '%s', please use a dot separated path or an array.",
                    gettype($path)
                ));
            }

            $parts = $path;
        }

        foreach ($parts as $part) {
            if (is_array($data) && isset($data[$part])) {
                $data = $data[$part];
            } else {
                return $default;
            }
        }

        return $data;
    }

    /**
     * Checks if the data exists at the given $path in the array $data. You can use a dot separated path.
     *
     * @param array        $data The data to look into
     * @param string|array $path The path to look at
     *
     * @return bool Whether or not there is valid data
     *
     * @throws InvalidArgumentException
     */
    public static function check(array $data, $path)
    {
        return (null !== self::get($data, $path, null));
    }

    /**
     * Inserts $value into an array given the $path. You can use a dot separated path.
     *
     * @param array        $data  The array to insert into
     * @param string|array $path  The path to insert at
     * @param mixed        $value The value to insert
     *
     * @return array The new data
     *
     * @throws InvalidArgumentException
     */
    public static function insert(array $data, $path, $value)
    {
        // Extract the parts
        if (is_string($path) || is_numeric($path)) {
            $parts = explode('.', $path);
        } else {
            if (!is_array($path)) {
                throw new InvalidArgumentException(sprintf(
                    "Invalid path parameter of type '%s', please use a dot separated path or an array.",
                    gettype($path)
                ));
            }

            $parts = $path;
        }

        // Recursive insertion into the array
        $k = array_shift($parts);
        if (count($parts) > 0) {
            if (isset($data[$k]) && !is_array($data[$k])) {
                throw new InvalidArgumentException("The path tries use the existing key '$k' but this key does not point on an array, it points on '$data[$k]'.");
            }
            if (!isset($data[$k])) {
                $data[$k] = [];
            }
            $data[$k] = self::insert($data[$k], $parts, $value);
        } else {
            $data[$k] = $value;
        }

        return $data;
    }

    /**
     * Removes an element from the array $data given the $path. You can use a dot separated path.
     *
     * @param array        $data The data you want to erase from
     * @param string|array $path The path you want to erase at
     *
     * @return array The new data
     *
     * @throws InvalidArgumentException
     */
    public static function erase(array $data, $path)
    {
        if (empty($data) || empty($path)) {
            return $data;
        }

        // Extract the parts
        if (is_string($path) || is_numeric($path)) {
            $parts = explode('.', $path);
        } else {
            if (!is_array($path)) {
                throw new InvalidArgumentException(sprintf(
                    "Invalid path parameter of type '%s', please use a dot separated path or an array.",
                    gettype($path)
                ));
            }

            $parts = $path;
        }

        // Recursive removal from the array
        $k = array_shift($parts);
        if (count($parts) > 0) {
            if (!isset($data[$k]) || !is_array($data[$k])) {
                throw new InvalidArgumentException("The path tries access the non-existing key '$k'.");
            }
            $data[$k] = self::erase($data[$k], $parts);
        } else {
            if (!isset($data[$k])) {
                throw new InvalidArgumentException("The path tries access the non-existing key '$k'.");
            }
            unset($data[$k]);
        }

        return $data;
    }
}
