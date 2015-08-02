<?php

namespace Phat\Event;


trait EventDispatcherTrait
{
    public function eventManager()
    {
        return EventManager::instance();
    }

    public function dispatchEvent($name, $data = null, $subject = null)
    {
        if(null === $subject) {
            $subject = $this;
        }

        $event = new Event($name, $data, $subject);
        $this->eventManager(); // TODO
    }
}