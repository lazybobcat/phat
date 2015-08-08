<?php

namespace Phat\ORM\Database;


use Phat\Core\Configure;
use Phat\ORM\Database\MySql\MySqlReflector;
use Phat\ORM\Table;

class TableHandler
{
    public function describe(Table $table, $database = 'default')
    {
        $driver = Configure::read("Databases.{$database}.driver");
        $reflector = null;

        switch($driver)
        {
            case 'mysql':
            default:
                $reflector = new MySqlReflector();
                break;
        }

        if(null === $reflector) {
            // TODO
            throw new \Exception("TODO");
        }

        return $reflector->columns($table, $database);
    }

    public function columnList(Table $table, $database = 'default')
    {
        $columns = $this->describe($table, $database);

        $list = [];
        foreach($columns as $col) {
            $list[] = $col->field;
        }

        return $list;
    }
}