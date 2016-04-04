<?php

namespace Phat\ORM;


use Phat\Utils\Inflector;
use Pixie\QueryBuilder\QueryBuilderHandler;

abstract class Association implements AssociationInterface
{
    protected $name;

    protected $alias;

    protected $propertyName;

    protected $database = 'default';

    protected $sourceTable;

    protected $targetTable;

    protected $sourceField;

    protected $targetField;

    protected $conditions = [];

    protected $joinType = 'LEFT';

    public function __construct($name, array $options = [])
    {
        $attrs = [
            'alias',
            'propertyName',
            'database',
            'sourceTable',
            'targetTable',
            'sourceField',
            'targetField',
            'conditions',
            'jointType'
        ];

        foreach($attrs as $a) {
            if(isset($options[$a])) {
                $this->{$a} = $options[$a];
            }
        }

        $this->name = $name;
        if(empty($this->alias)) {
            $this->alias = Inflector::singularize(strtolower($this->name));
        }
    }

    public function alter(QueryBuilderHandler $qb)
    {
        // todo

        return $qb;
    }

    /**
     * @return mixed
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @return mixed
     */
    public function getAlias()
    {
        return $this->alias;
    }

    /**
     * @return mixed
     */
    public function getSourceTable()
    {
        return $this->sourceTable;
    }

    /**
     * @return mixed
     */
    public function getTargetTable()
    {
        return $this->targetTable;
    }


}