<?php

namespace Phat\ORM;


interface EntityInterface extends \JsonSerializable
{
    public function toArray();
}