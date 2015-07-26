<?php

namespace Phat\View;

use Phat\Core\Exception\LogicErrorException;

class ViewBlock
{
    // TODO : start(...) and end()

    private $blocks = [];
    private $activeBlocks = [];

    const OVERRIDE = 'override';
    const APPEND = 'append';
    const PREPEND = 'prepend';

    /**
     * Sets the block $name content to $value.
     *
     * @param string $name
     * @param string $value
     */
    final public function set($name, $value)
    {
        $this->blocks[$name] = (string) $value;
    }

    final public function get($name, $default = '')
    {
        if (!isset($this->blocks[$name])) {
            return $default;
        }

        return $this->blocks[$name];
    }

    final public function start($name, $mode = self::OVERRIDE)
    {
        if (in_array($name, array_keys($this->activeBlocks))) {
            throw new LogicErrorException("A block with the name '$name' is already open.");
        }

        $this->activeBlocks[$name] = $mode;
        ob_start();
    }

    final public function end()
    {
        if (empty($this->activeBlocks)) {
            throw new LogicErrorException('There are no opened blocks.');
        }

        $mode = end($this->activeBlocks);
        $active = key($this->activeBlocks);
        $content = ob_get_clean();
        if ($mode === self::OVERRIDE) {
            $this->blocks[$active] = $content;
        } else {
            $this->concat($active, $content, $mode);
        }
        array_pop($this->activeBlocks);
    }

    final public function concat($name, $value = null, $mode = self::APPEND)
    {
        if (empty($value)) {
            $this->start($name, $mode);

            return;
        }

        if (!isset($this->blocks[$name])) {
            $this->blocks[$name] = '';
        }
        if ($mode === self::PREPEND) {
            $this->blocks[$name] = $value.$this->blocks[$name];
        } else {
            $this->blocks[$name] .= $value;
        }
    }

    public function unclosed()
    {
        return $this->activeBlocks;
    }
}
