<?php

namespace Phat\Event;


class EventManager
{
    public static $defaultPriority = 10;

    protected static $instance = null;

    protected $listeners = [];

    public static function instance()
    {
        if(null === self::$instance) {
            self::$instance = new EventManager();
        }

        return self::$instance;
    }

    public function dispatch(Event $event)
    {
        $listeners = $this->listeners($event->getName());

        if(empty($listeners)) {
            return $event;
        }

        foreach ($listeners as $listener) {
            if($event->isStopped()) {
                break;
            }
            $data = $event->data();
            if(count($data)) {
                $data = array_values($data);
            }
            array_unshift($data, $event);
            $result = call_user_func_array($listener, $data);

            if($result === false) {
                $event->stopPropagation();
            }
            if($result !== null) {
                $event->result = $result;
            }
        }

        return $event;
    }

    public function listeners($event_name)
    {
        if(!isset($this->listeners[$event_name])) {
            return [];
        }

        $listeners_prio_list = $this->listeners[$event_name];
        arsort($listeners_prio_list);
        $listeners = [];

        foreach ($listeners_prio_list as $listeners_list) {
            foreach($listeners_list as $l) {
                $listeners[] = $l;
            }
        }

        return $listeners;
    }

    public function attach(EventListenerInterface $instance)
    {
        foreach($instance->implementedEvents() as $event_name => $function) {
            $options = [];
            $method = $function;
            if(is_array($function) && isset($function['callable'])) {
                list($method, $options) = $this->extractCallable($function, $instance);
            } elseif(is_array($function) && is_numeric(key($function))) {
                foreach($function as $func) {
                    list($method, $options) = $this->extractCallable($func, $instance);
                    $this->on($event_name, $method, $options);
                }
                continue;
            }
            if(is_string($method)) {
                $method = [$instance, $method];
            }
            $this->on($event_name, $method, $options);
        }
    }

    public function detach(EventListenerInterface $instance)
    {
        foreach($instance->implementedEvents() as $event_name => $function) {
            if(is_array($function)) {
                if(is_numeric(key($function))) {
                    foreach($function as $func) {
                        $func = isset($func['callable']) ? $func['callable'] : $func;
                        $this->off($event_name, [$instance, $func]);
                    }
                    continue;
                }
                $function = $function['callable'];
            }
            $this->off($event_name, [$instance, $function]);
        }
    }

    private function extractCallable($function, $instance)
    {
        $method = $function['callable'];
        $options = $function;
        unset($options['callable']);
        if(is_string($method)) {
            $method = [$instance, $method];
        }

        return [$method, $options];
    }

    private function on($event_name, $callable, $options = [])
    {
        if(!isset($options['priority'])) {
            $options['priority'] = self::$defaultPriority;
        }

        $this->listeners[$event_name][$options['priority']][] = $callable;
    }

    private function off($event_name, $callable)
    {
        foreach ($this->listeners[$event_name] as $priority => $callables) {
            foreach ($callables as $k => $callback) {
                if ($callback['callable'] === $callable) {
                    unset($this->listeners[$event_name][$priority][$k]);
                    break;
                }
            }
        }
    }
}