<?php

namespace Phat\Test\Event;


use Phat\Event\Event;
use Phat\Event\EventListenerInterface;
use Phat\Event\EventManager;
use Phat\TestTool\TestCase;

class EventTestListener
{
    public $callstack = [];

    public function listener()
    {
        $this->callstack[] = __FUNCTION__;
    }

    public function secondListener()
    {
        $this->callstack[] = __FUNCTION__;
    }

    public function stop($event)
    {
        $event->stopPropagation();
        $this->callstack[] = __FUNCTION__;
    }

    public function stopfalse()
    {
        $this->callstack[] = __FUNCTION__;
        return false;
    }
}

class EventTestListenerChild extends EventTestListener implements EventListenerInterface
{
    public function implementedEvents()
    {
        return [
            'first.event' => 'listener',
            'second.event' => ['callable' => 'secondListener'],
            'all.event' => ['listener', 'secondListener', 'thirdListener'],
            'stop.event' => ['listener', 'stop', 'secondListener'],
            'stopfalse.event' => ['listener', 'stopfalse', 'secondListener'],
        ];
    }

    public function thirdListener()
    {
        $this->callstack[] = __FUNCTION__;
    }
}

class EventManagerTest extends TestCase
{
    public function testInstance()
    {
        $em1 = EventManager::instance();
        $this->assertInstanceOf('\Phat\Event\EventManager', $em1);

        $em2 = EventManager::instance();
        $this->assertSame($em1, $em2);
    }

    public function testDispatch()
    {
        $em = EventManager::instance();
        $listener = new EventTestListenerChild();
        $anotherListener = new EventTestListenerChild();

        $em->attach($listener);
        $em->attach($anotherListener);
        $event = new Event('first.event');

        $em->dispatch($event);

        $expected = ['listener'];
        $this->assertEquals($expected, $listener->callstack);
        $this->assertEquals($expected, $anotherListener->callstack);
    }

    // TODO
}