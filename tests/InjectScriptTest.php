<?php

namespace IsaEken\Proxier\Tests;

use IsaEken\Proxier\Features\InjectScript;
use PHPUnit\Framework\TestCase;

class InjectScriptTest extends TestCase
{
    public function testInjectScript(): void
    {
        $script = 'alert("Hello World!");';
        $html = '<html><head></head><body><h1>hello world</h1></body></html>';
        $expected = '<html><head></head><body><h1>hello world</h1><script>alert("Hello World!");</script></body></html>';

        $injectScript = new InjectScript();
        $this->assertEquals($expected, $injectScript->inject($script, $html));
    }
}
