<?php

namespace Phat\Event;


class Event
{
    protected $name = null;

    protected $subject = null;

    protected $stopped = false;

    public $data = null;

    public $result = null;


    public function __construct($name, $data = null, $subject = null)
    {
        $this->name = $name;
        $this->data = $data;
        $this->subject = $subject;
    }

    public function __get($attr)
    {
        if('name' === $attr || 'subject' === $attr) {
            return $this->get{ucfirst($attr)}();
        }

        return null;
    }

    /**
     * @return null
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @return null
     */
    public function getSubject()
    {
        return $this->subject;
    }

    public function stopPropagation()
    {
        $this->stopped = true;
    }

    public function isStopped()
    {
        return $this->stopped;
    }

    public function data()
    {
        return (array)$this->data;
    }


}