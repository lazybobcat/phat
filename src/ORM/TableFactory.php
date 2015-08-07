<?php

namespace Phat\ORM;


use Phat\ORM\Exception\MissingTableException;

class TableFactory
{
    private static $tables = [];
    private static $options = [];


    /**
     * @param $alias
     * @param array $options
     * @return Table
     * @throws MissingTableException
     */
    public static function get($alias, array $options = [])
    {
        if(isset(self::$tables[$alias])) {
            if(!empty($options) && self::$options[$alias] !== $options) {
                throw new \RuntimeException(
                    sprintf("You cannot give options to '%s' because it has already been constructed.", $alias)
                );
            }

            return self::$tables[$alias];
        }

        if(isset($options['className'])) {
            $table = new $options['className']($options);
        } else {
            $table = 'App\Model\Table\\'.ucfirst(strtolower($alias)).'Table';
            if(!class_exists($table)) {
                throw new MissingTableException(
                    sprintf(
                        "The table class for '%s' has not been found. Please set the option 'className' with the full namespaced  table class.",
                        $alias
                    )
                );
            }
            $table = new $table($options);
        }

        self::$tables[$alias] = $table;
        self::$options[$alias] = $options;

        return $table;
    }
}