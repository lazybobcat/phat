<?php

namespace Phat\ORM\Database;


use Phat\ORM\Table;

interface TableReflector
{
    /**
     * @param Table $table
     * @param array $connection_config
     * @return array
     */
    public function columns(Table $table, $database);
}