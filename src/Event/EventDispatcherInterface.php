<?php

namespace Phat\Event;


interface EventDispatcherInterface
{
    /**
     * Creates and dispatch an Event.
     *
     * @param string $name Name of the event
     * @param array|null $data The data you want to be accessed in listeners
     * @param object|null $subject The object concerned by the event. Default: $this
     *
     * @return Event
     */
    public function dispatchEvent($name, $data = null, $subject = null);

    /**
     * Returns the Phat\Event\EventManager instance. You can use this instance to register callbacks.
     *
     * @return EventManager
     */
    public function eventManager();
}