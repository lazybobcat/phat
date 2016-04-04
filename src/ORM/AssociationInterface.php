<?php

namespace Phat\ORM;


interface AssociationInterface
{
    public function join(Query $query);

    public function hydrate($repository_alias, array $data, Entity $sourceEntity);
}