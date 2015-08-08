<?php

namespace Phat\ORM\Database\MySql;


use Phat\ORM\Database\Column;
use Phat\ORM\Database\Connection;
use Phat\ORM\Database\TableReflector;
use Phat\ORM\Table;

class MySqlReflector implements TableReflector
{
    private $columns = [];

    /**
     * @param Table $table
     * @param $database
     * @throws \Exception
     * @return array
     */
    public function columns(Table $table, $database)
    {
        if(empty($this->columns[$table->alias()])) {
            $qb = Connection::getQueryBuilder($database);
            $results = $qb->query("DESCRIBE {$table->table()}")->get();

            if(empty($results)) {
                // TODO
                throw new \Exception("TODO");
            }

            $columns = [];
            foreach($results as $result) {
                $column = new Column();
                $column->field = $result->Field;
                $column->type = $result->Type;
                $column->null = ($result->Field == 'NO' ? false : true);
                $column->default = $result->Default;
                $columns[] = $column;
            }
            $this->columns[$table->alias()] = $columns;
        }

        return $this->columns[$table->alias()];
    }
}