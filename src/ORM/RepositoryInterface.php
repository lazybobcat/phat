<?php

namespace Phat\ORM;


interface RepositoryInterface
{
    public function alias();

    public function query();

    public function newEntity(array $data = [], array $options = []);

    public function find($type = 'all', array $options = []);

    public function findById($id, array $options = []);

    public function save(EntityInterface $entity, array $options = []);

    public function updateAll($fields, array $conditions);

    public function delete(EntityInterface $entity, array $options = []);

    public function deleteAll(array $conditions);
}