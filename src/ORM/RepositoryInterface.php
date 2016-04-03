<?php

namespace Phat\ORM;


interface RepositoryInterface
{
    public function alias();

    public function query();

    public function create(array $data = []);

    public function findAll();

    public function find($id);

    public function save(EntityInterface $entity, array $options = []);

    public function updateAll($fields, array $conditions);

    public function delete(EntityInterface $entity, array $options = []);

    public function deleteAll(array $conditions);
}