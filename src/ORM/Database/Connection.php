<?php

namespace Phat\ORM\Database;


use Phat\Core\Configure;
use Pixie\QueryBuilder\QueryBuilderHandler;

class Connection
{
    private static $connections = [];

    public static function get($database = 'default')
    {
        if(empty(self::$connections[$database])) {
            $config = Configure::read("Databases.{$database}");
            self::$connections[$database] = new \Pixie\Connection($config['driver'], $config);
        }

        return self::$connections[$database];
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