<?php

namespace Phat\View;


class ViewBlock
{
    // TODO : start(...) and end()

    private $blocks = [];

    public final function set($name, $value)
    {
        $this->blocks[$name] = (string)$value;
    }

    public final function get($name, $default = '')
    {
        if(!isset($this->blocks[$name])) {
            return $default;
        }

        return $this->blocks[$name];
    }
}