<?php

namespace Phat\Test\Event;


use Phat\TestTool\TestCase;

class EventDispatcherTraitTest extends TestCase
{
    private $subject = null;

    public function setUp()
    {
        $this->subject = $this->getObjectForTrait('\Phat\Event\EventDispatcherTrait');
    }

    public function testEventManager()
    {
        $em = $this->subject->eventManager();
        $this->assertInstanceOf('\Phat\Event\EventManager', $em);
    }

    public function testDispatchEvent()
    {
        $event = $this->subject->dispatchEvent('Ultimate.Question', ['answer' => 42]);

        $this->assertInstanceOf('\Phat\Event\Event', $event);
        $this->assertSame($this->subject, $event->subject);
        $this->assertEquals('Ultimate.Question', $event->name);
        $this->assertEquals(['answer' => 42], $event->data);
    }
}