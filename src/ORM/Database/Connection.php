<?php

namespace Phat\ORM\Database;


use Phat\Core\Configure;
use Pixie\QueryBuilder\QueryBuilderHandler;

class Connection
{
    private static $connection = null;

    public static function get($database = 'default')
    {
        if(null === self::$connection) {
            $config = Configure::read("Databases.{$database}");
            self::$connection = new \Pixie\Connection($config['driver'], $config);
        }

        return self::$connection;
    }

    public static function getQueryBuilder($database = 'default')
    {
        $pixie = self::get($database);
        return new QueryBuilderHandler($pixie);
    }


    private function __construct()
    {
    }
}