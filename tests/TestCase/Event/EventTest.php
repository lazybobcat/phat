<?php

namespace Phat\Test\Event;


use Phat\Event\Event;
use Phat\TestTool\TestCase;

class EventTest extends TestCase
{
    public function testName()
    {
        $event = new Event('Some.Name');
        $this->assertEquals('Some.Name', $event->getName());
        $this->assertEquals('Some.Name', $event->name);
    }

    public function testSubject()
    {
        $event = new Event('Some.Name');
        $this->assertNull($event->getSubject());

        $event = new Event('Some.Name', null, $this);
        $this->assertSame($this, $event->getSubject());
        $this->assertSame($this, $event->subject);
    }

    public function testStopped()
    {
        $event = new Event('Some.Name');
        $this->assertFalse($event->isStopped());
        $event->stopPropagation();
        $this->assertTrue($event->isStopped());
    }

    public function testEventData()
    {
        $event = new Event('Some.Name', ['answer' => 42]);
        $this->assertEquals(['answer' => 42], $event->data());
    }
}