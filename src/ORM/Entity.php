<?php

namespace Phat\ORM;


class Entity implements EntityInterface
{
    use EntityTrait;

    public function __construct(array $properties = [])
    {
        foreach($properties as $prop => $val) {
            $this->$prop = $val;
        }
    }
}