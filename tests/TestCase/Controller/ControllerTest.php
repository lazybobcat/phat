<?php

namespace Phat\Test\TestCase\Controller;

use Phat\Controller\Controller;
use Phat\Http\Request;
use Phat\TestTool\TestCase;
use Phat\View\View;

class TestController extends Controller
{
    public function getRequest()
    {
        return $this->request;
    }

    public function getName()
    {
        return $this->name;
    }

    public function getView()
    {
        return $this->view;
    }
}

class ControllerTest extends TestCase
{
    private $request;

    public function setUp()
    {
        $_SERVER['REQUEST_METHOD'] = 'GET';
        $this->request = new Request();
        $this->request->action = 'index';
    }

    public function testConstruction()
    {
        if (ini_get('safe_mode') === '1') {
            $this->markTestSkipped('Safe Mode is on');
        }

        $Controller = new TestController($this->request);
        $this->assertEquals($this->request, $Controller->getRequest());
        $this->assertEquals('Test', $Controller->getName());
        $this->assertTrue($Controller->getView() instanceof View);
    }

    public function testRender()
    {
        // TODO
    }

    public function testE404()
    {
        // TODO
    }
}
