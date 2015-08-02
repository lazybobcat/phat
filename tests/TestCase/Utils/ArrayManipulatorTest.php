<?php

namespace Phat\Test\Utils;


use Phat\TestTool\TestCase;
use Phat\Utils\ArrayManipulator;

class ArrayManipulatorTest extends TestCase
{
    private $data = [];

    public function setUp()
    {
        $this->data = [
            'Test' => [
                'Post' => [
                    'id' => 42,
                    'name' => "The ultimate question about life, the universe and everything",
                ],
                'Config' => [
                    'encoding' => 'utf-8',
                    'testsDir' => 'tests'
                ]
            ]
        ];
    }

    public function testGet()
    {
        $expected = 'utf-8';
        $result = ArrayManipulator::get($this->data, 'Test.Config.encoding');
        $this->assertEquals($expected, $result);

        $expected = [
            'id' => 42,
            'name' => "The ultimate question about life, the universe and everything",
        ];
        $result = ArrayManipulator::get($this->data, 'Test.Post');
        $this->assertEquals($expected, $result);

        $result = ArrayManipulator::get($this->data, 'Test.Return.me.null');
        $this->assertNull($result);

        $result = ArrayManipulator::get($this->data, 'Test.Return.me.true', true);
        $this->assertTrue($result);
    }

    /**
     * @expectedException \Phat\Utils\Exception\InvalidArgumentException
     * @expectedExceptionMessage Invalid path parameter of type 'object', please use a dot separated path or an array.
     */
    public function testGetInvalidPathException()
    {
        // This should raise an exception
        $path = new \DateTime('now');
        ArrayManipulator::get($this->data, $path);
    }

    public function testCheck()
    {
        $result = ArrayManipulator::check($this->data, 'Test.Config.encoding');
        $this->assertTrue($result);

        $result = ArrayManipulator::check($this->data, 'Test.Non.Existing.Path');
        $this->assertFalse($result);
    }

    /**
     * @expectedException \Phat\Utils\Exception\InvalidArgumentException
     * @expectedExceptionMessage Invalid path parameter of type 'object', please use a dot separated path or an array.
     */
    public function testCheckInvalidPathException()
    {
        // This should raise an exception
        $path = new \DateTime('now');
        ArrayManipulator::check($this->data, $path);
    }

    public function testInsert()
    {
        $this->data = ArrayManipulator::insert($this->data, 'NewKey', 'NewValue');
        $result = ArrayManipulator::check($this->data, 'NewKey');
        $this->assertTrue($result);

        $this->data = ArrayManipulator::insert($this->data, 'Test.Post.created', new \DateTime('now'));
        $result = ArrayManipulator::check($this->data, 'Test.Post.created');
        $this->assertTrue($result);
    }

    /**
     * @expectedException \Phat\Utils\Exception\InvalidArgumentException
     * @expectedExceptionMessage Invalid path parameter of type 'object', please use a dot separated path or an array.
     */
    public function testInsertInvalidPathException()
    {
        $path = new \DateTime('now');
        ArrayManipulator::insert($this->data, $path, 42);
    }

    /**
     * @expectedException \Phat\Utils\Exception\InvalidArgumentException
     * @expectedExceptionMessage The path tries use the existing key 'SubKey' but this key does not point on an array, it points on 'AnotherValue'.
     */
    public function testInsertExistingPathException()
    {
        $this->data = ArrayManipulator::insert($this->data, 'NewKey.SubKey', 'AnotherValue');
        $this->data = ArrayManipulator::insert($this->data, 'NewKey.SubKey.Data', 'NewValue');
    }

    public function testErase()
    {
        $this->data = ArrayManipulator::erase($this->data, 'Test.Post.id');
        $this->assertFalse(ArrayManipulator::check($this->data, 'Test.Post.id'));

        $this->data = ArrayManipulator::erase($this->data, 'Test.Config');
        $this->assertFalse(ArrayManipulator::check($this->data, 'Test.Config'));
    }

    /**
     * @expectedException \Phat\Utils\Exception\InvalidArgumentException
     * @expectedExceptionMessage Invalid path parameter of type 'object', please use a dot separated path or an array.
     */
    public function testEraseInvalidPathException()
    {
        $path = new \DateTime('now');
        ArrayManipulator::erase($this->data, $path);
    }

    /**
     * @expectedException \Phat\Utils\Exception\InvalidArgumentException
     * @expectedExceptionMessage The path tries access the non-existing key 'SubKey'.
     */
    public function testEraseExistingPathException()
    {
        $this->data = ArrayManipulator::insert($this->data, 'NewKey.SubKey', 'AnotherValue');
        $this->data = ArrayManipulator::erase($this->data, 'NewKey.SubKey.Data');
    }
}