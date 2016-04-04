<?php

namespace Phat\ORM\Association;


use Phat\ORM\Association;
use Phat\ORM\Entity;
use Phat\ORM\Query;

class HasMany extends Association
{
    protected $joinType = 'INNER';

    public function join(Query $query)
    {
        $targetField = $this->targetTable->aliasField($this->targetField);

        $query->join($this->targetTable, $this->sourceField, '=', $targetField, $this->joinType)
            ->select($this->targetTable)
        ;
    }

    public function hydrate($repository_alias, array $data, Entity $sourceEntity)
    {
        if($repository_alias != $this->targetTable->repositoryAlias()) {
            return null;
        }

        $entity = $this->targetTable->hydrate($data);

        $method = 'set'.ucfirst($this->propertyName);
        if(method_exists($sourceEntity, $method)) {
            $sourceEntity->{$method}($entity);
        } else {
            $sourceEntity->{$this->propertyName} = $entity;
        }

        unset($sourceEntity->{$this->sourceField});

        return $sourceEntity;
    }
}