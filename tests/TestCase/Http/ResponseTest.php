<?php

namespace Phat\Test\TestCase\Http;


use Phat\Http\Response;
use Phat\TestTool\TestCase;

class ResponseTest extends TestCase
{

    public function testConstruction()
    {
        $response = new Response();
        $this->assertNull($response->getBody());
        $this->assertNull($response->getFile());
        $this->assertEquals('utf-8', $response->getCharset());
        $this->assertEquals('HTTP/1.1', $response->getProtocol());
        $this->assertEquals(200, $response->getStatus());
        $this->assertEquals('text/html', $response->getContentType());
        $this->assertEmpty($response->getHeaders());
        $this->assertEmpty($response->getCookies());

        $options = [
            'body' => '{"hello": "world"}',
            'status' => 302,
            'contentType' => 'json',
            'charset' => 'utf-16'
        ];

        $response = new Response($options);
        $this->assertEquals('{"hello": "world"}', $response->getBody());
        $this->assertEquals(302, $response->getStatus());
        $this->assertEquals('application/json', $response->getContentType());
        $this->assertEquals('utf-16', $response->getCharset());
    }

    public function testSend()
    {
        // TODO
    }

    public function testSetProtocol()
    {
        $response = new Response();
        $response->setProtocol('TEST/1.1');
        $this->assertEquals('TEST/1.1', $response->getProtocol());
    }


    public function testSetStatus()
    {
        $response = new Response();
        $response->setStatus(404);
        $this->assertEquals(404, $response->getStatus());
    }

    /**
     * @expectedException \Phat\Http\Exception\UnknownStatusException
     * @expectedExceptionMessage Unknown HTTP status '42'
     */
    public function testSetStatusException()
    {
        $response = new Response();
        $response->setStatus(42);
    }

    public function testSetContentType()
    {
        $response = new Response();

        $response->setContentType('pdf');
        $this->assertEquals('application/pdf', $response->getContentType());

        $response->setContentType('json');
        $this->assertEquals('application/json', $response->getContentType());

        $response->setContentType('html');
        $this->assertEquals('text/html', $response->getContentType());
    }

    public function testAddHeader()
    {
        $response = new Response();
        $result = $response->getHeaders();
        $this->assertEmpty($result);

        $expected = ['Content Type' => 'text/html'];
        $response->addHeader('Content Type', 'text/html');
        $result = $response->getHeaders();
        $this->assertEquals($expected, $result);

        $expected += ['Location' => 'www.loicboutter.fr'];
        $response->addHeader('Location', 'www.loicboutter.fr');
        $result = $response->getHeaders();
        $this->assertEquals($expected, $result);
    }

    public function setBody()
    {
        $response = new Response();
        $response->setBody("<p>Hello World!</p>");
        $this->assertEquals("<p>Hello World!</p>", $response->getBody());
    }

}