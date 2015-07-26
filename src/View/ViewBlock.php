<?php

namespace Phat\View;

use Phat\Core\Exception\LogicErrorException;

class ViewBlock
{
    /**
     * @var array Contains the blocks content indexed by block name
     */
    private $blocks = [];

    /**
     * @var array Contains the currently active (buffering) blocks indexed by block name
     */
    private $activeBlocks = [];

    /**
     * Override content.
     */
    const OVERRIDE = 'override';

    /**
     * Append content.
     */
    const APPEND = 'append';

    /**
     * Prepend content.
     */
    const PREPEND = 'prepend';

    /**
     * Sets the block $name content to $value.
     * This will override ay existing block content.
     *
     * @param string $name  The block name
     * @param string $value The value to put in the block
     */
    final public function set($name, $value)
    {
        $this->blocks[$name] = (string) $value;
    }

    /**
     * Gets the content of the block $name.
     * If no block is found, returns $default value.
     *
     * @param string $name    Name of the block
     * @param string $default The default value
     *
     * @return string The block content
     */
    final public function get($name, $default = '')
    {
        if (!isset($this->blocks[$name])) {
            return $default;
        }

        return $this->blocks[$name];
    }

    /**
     * Start buffering output to put it in the block $name.
     * You have to stop the buffering by calling ViewBlock::end(). Then you will be able to get the content with ViewBlock::get().
     *
     * @param string $name The name of the block
     * @param string $mode The writing mode: override content, append or prepend to content
     *
     * @throws LogicErrorException
     */
    final public function start($name, $mode = self::OVERRIDE)
    {
        if (in_array($name, array_keys($this->activeBlocks))) {
            throw new LogicErrorException("A block with the name '$name' is already open.");
        }

        $this->activeBlocks[$name] = $mode;
        ob_start();
    }

    /**
     * Ends a buffered block started with ViewBlock::start().
     *
     * @throws LogicErrorException
     */
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

    /**
     * Does a concatenation to an existing block. Can append or prepend.
     * If the value is null, a buffered block will start, you need to end it with ViewBlock::end().
     *
     * @param string      $name  The name of the block
     * @param string|null $value The value to append/prepend
     * @param string      $mode  ViewBlock::APPEND or ViewBlock::PREPEND
     *
     * @throws LogicErrorException
     */
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

    /**
     * Returns currently active buffering blocks.
     *
     * @return array
     */
    public function unclosed()
    {
        return $this->activeBlocks;
    }

    /**
     * Returns the name of the last active block.
     *
     * @return mixed
     */
    public function active()
    {
        end($this->activeBlocks);

        return key($this->activeBlocks);
    }
}
