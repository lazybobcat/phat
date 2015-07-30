<?php

namespace Phat\Test;

use Phat\Core\Configure;
use Phat\TestTool\TestCase;

class CommonTest extends TestCase
{

    public function testDebug()
    {
        $expected = <<< EOT
<pre>

> \$object - %s:%d
=========================================================
string(12) "Hello World!"
</pre>
EOT;

        $expected = sprintf($expected, __FILE__, __LINE__ + 2);
        $this->expectOutputString($expected);
        debug("Hello World!", false); // Text mode
    }

    public function testDev()
    {
        Configure::write('debug', true);
        $this->assertEquals(true, dev());

        Configure::write('debug', false);
        $this->assertEquals(false, dev());
    }

    public function testNamespaceSplit()
    {
        $result = namespaceSplit("Hey");
        $this->assertEquals(['Hey'], $result);

        $result = namespaceSplit("\Hey");
        $this->assertEquals(['Hey'], $result);

        $result = namespaceSplit("Hey\Ho");
        $this->assertEquals(['Hey', 'Ho'], $result);

        $result = namespaceSplit("\Hey\Ho\You");
        $this->assertEquals(['Hey', 'Ho', 'You'], $result);
    }

}