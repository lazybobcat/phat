<?php

namespace Phat\Test\TestCase\Core;

use Phat\Core\Configure;
use Phat\TestTool\TestCase;

class ConfigureTest extends TestCase
{
    private static $backup;

    public static function setUpBeforeClass()
    {
        self::$backup = Configure::read();
    }

    public static function tearDownAfterClass()
    {
        Configure::write(self::$backup);
    }

    public function setUp()
    {
        Configure::clear();
    }

    public function testWrite()
    {
        $result = Configure::write('ThisIsAKey', 'ThisIsAValue');
        $this->assertTrue($result);
        $result = Configure::read('ThisIsAKey');
        $this->assertEquals('ThisIsAValue', $result);

        $expected = ['One' => ['Two' => ['Three' => "Let's Party!"]]];
        $result = Configure::write('Song', $expected);
        $this->assertTrue($result);

        $result = Configure::read('Song');
        $this->assertEquals($expected, $result);
    }

    public function testRead()
    {
        Configure::write('Test', 'ok');
        $result = Configure::read('Test');
        $this->assertEquals('ok', $result);

        $expected = ['One' => ['Two' => ['Three' => "Let's Party!"]]];
        Configure::write('Song', $expected);

        $result = Configure::read();
        $this->assertTrue(is_array($result));
        $this->assertTrue(isset($result['Song']));
        $this->assertTrue(isset($result['Song']['One']));
        $this->assertTrue(isset($result['Song']['One']['Two']));
        $this->assertTrue(isset($result['Song']['One']['Two']['Three']));
        $this->assertEquals("Let's Party!", $result['Song']['One']['Two']['Three']);
    }

    public function testCheck()
    {
        Configure::write('Test', 'ok');
        $result = Configure::check('Test');
        $this->assertTrue($result);

        $result = Configure::check('TestBis');
        $this->assertFalse($result);

        $array = ['One' => ['Two' => ['Three' => "Let's Party!"]]];
        Configure::write('Song', $array);
        $result = Configure::check('Song');
        $this->assertTrue($result);
    }

    public function testErase()
    {
        Configure::write('Test', 'ok');
        $result = Configure::check('Test');
        $this->assertTrue($result);

        Configure::erase('Test');
        $result = Configure::check('Test');
        $this->assertFalse($result);

        //

        Configure::write('TestArray', ['a' => 'Apple', 'b' => 'Boat']);
        $result = Configure::check('TestArray');
        $this->assertTrue($result);

        Configure::erase('TestArray');
        $result = Configure::check('TestArray');
        $this->assertFalse($result);
    }

    public function testClear()
    {
        Configure::write('Test', 'ok');
        $result = Configure::check('Test');
        $this->assertTrue($result);

        Configure::write('TestArray', ['a' => 'Apple', 'b' => 'Boat']);
        $result = Configure::check('TestArray');
        $this->assertTrue($result);

        Configure::clear();
        $this->assertTrue(empty(Configure::read()));
        $this->assertFalse(Configure::check('Test'));
        $this->assertFalse(Configure::check('TestArray'));
    }
}
