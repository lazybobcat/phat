<?php

namespace Phat\ORM;


trait EntityTrait
{
    /**
     * (PHP 5 &gt;= 5.4.0)<br/>
     * Specify data which should be serialized to JSON
     * @link http://php.net/manual/en/jsonserializable.jsonserialize.php
     * @return mixed data which can be serialized by <b>json_encode</b>,
     * which is a value of any type other than a resource.
     */
    public function jsonSerialize()
    {
        return $this->toArray();
    }

    public function toArray()
    {
        $result = [];
        $attributes = get_object_vars($this);

        foreach($attributes as $attr => $val) {
            if(is_array($val)) {
                $result[$attr] = [];
                foreach($val as $k => $v) {
                    if($v instanceof EntityInterface) {
                        $result[$attr][$k] = $v->toArray();
                    } else {
                        $result[$attr][$k] = $v;
                    }
                }
            } elseif($val instanceof EntityInterface) {
                $result[$attr] = $val->toArray();
            } else {
                $result[$attr] = $val;
            }
        }

        return $result;
    }

    public function __toString()
    {
        return json_encode($this, JSON_PRETTY_PRINT);
    }
}