<?php

namespace Phat\Test\TestCase\Http;


use Phat\Http\Request;
use Phat\TestTool\TestCase;

class RequestTest extends TestCase
{
    public function testConstruction()
    {
        $_SERVER['REQUEST_METHOD'] = 'GET';
        $_SERVER['REQUEST_URI'] = '/test/request';
        $_POST = ['Data' => ['id' => 3]];

        $request = new Request();

        $this->assertEquals('get', $request->method);
        $this->assertEquals(Request::Get, $request->method);
        $this->assertEquals('/test/request', $request->url);
        $this->assertTrue(is_array($request->data));
        $this->assertEquals(['id' => 3], $request->data['Data']);
    }
}