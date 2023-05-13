<?php

namespace IsaEken\Proxier\Tests;

use GuzzleHttp\Psr7\Request;
use IsaEken\Proxier\Exceptions\BlockedException;
use IsaEken\Proxier\Features\Blocker;
use PHPUnit\Framework\TestCase;

class BlockerTest extends TestCase
{
    public function testHostGuard(): void
    {
        // test is blocking

        $blocker = new Blocker();
        $blocker->blockHosts([
            'example.com',
            'example.org',
            '*.example.net',
        ]);

        $this->assertFalse($blocker->isAllowedHosts('example.com'));
        $this->assertFalse($blocker->isAllowedHosts('example.org'));
        $this->assertTrue($blocker->isAllowedHosts('example.net'));
        $this->assertFalse($blocker->isAllowedHosts('test.example.net'));

        // test is allowing

        $blocker = new Blocker();
        $blocker->allowHosts([
            'example.com',
            'example.org',
            '*.example.net',
        ]);

        $this->assertTrue($blocker->isAllowedHosts('example.com'));
        $this->assertTrue($blocker->isAllowedHosts('example.org'));
        $this->assertFalse($blocker->isAllowedHosts('example.net'));
        $this->assertTrue($blocker->isAllowedHosts('test.example.net'));

        // test allow priority

        $blocker = new Blocker();
        $blocker->allowHosts([
            'example.com',
            '*.test.example.xyz',
            'example.net',
        ]);
        $blocker->blockHosts([
            'example.net',
            'example.org',
            '*.example.org',
            'test.test.example.xyz',
        ]);

        $this->assertTrue($blocker->isAllowedHosts('example.com'));
        $this->assertTrue($blocker->isAllowedHosts('testing.test.example.xyz'));
        $this->assertFalse($blocker->isAllowedHosts('example.net'));
        $this->assertFalse($blocker->isAllowedHosts('example.org'));
        $this->assertFalse($blocker->isAllowedHosts('test.example.org'));
        $this->assertFalse($blocker->isAllowedHosts('test.test.example.xyz'));
        $this->assertTrue($blocker->isAllowedHosts('testing.test.example.xyz'));
  }

    public function testMethodGuard(): void
    {
        // test is blocking

        $blocker = new Blocker();
        $blocker->blockMethods([
            'POST',
            'PUT',
            'DELETE',
        ]);

        $this->assertFalse($blocker->isAllowedMethod('POST'));
        $this->assertFalse($blocker->isAllowedMethod('PUT'));
        $this->assertFalse($blocker->isAllowedMethod('DELETE'));
        $this->assertTrue($blocker->isAllowedMethod('GET'));

        // test is allowing

        $blocker = new Blocker();
        $blocker->allowMethods([
            'POST',
            'PUT',
            'DELETE',
        ]);

        $this->assertTrue($blocker->isAllowedMethod('POST'));
        $this->assertTrue($blocker->isAllowedMethod('PUT'));
        $this->assertTrue($blocker->isAllowedMethod('DELETE'));
        $this->assertFalse($blocker->isAllowedMethod('GET'));

        // test allow priority

        $blocker = new Blocker();
        $blocker->allowMethods([
            'POST',
            'PUT',
        ]);
        $blocker->blockMethods([
            'GET',
            'HEAD',
        ]);

        $this->assertTrue($blocker->isAllowedMethod('POST'));
        $this->assertTrue($blocker->isAllowedMethod('PUT'));
        $this->assertFalse($blocker->isAllowedMethod('DELETE'));
        $this->assertFalse($blocker->isAllowedMethod('GET'));
        $this->assertFalse($blocker->isAllowedMethod('HEAD'));
    }

    public function testContentTypeGuard(): void
    {
        // test is blocking

        $blocker = new Blocker();
        $blocker->blockContentTypes([
            'application/json',
            'application/xml',
            'application/x-www-form-urlencoded',
        ]);

        $this->assertFalse($blocker->isAllowedContentType('application/json'));
        $this->assertFalse($blocker->isAllowedContentType('application/xml'));
        $this->assertFalse($blocker->isAllowedContentType('application/x-www-form-urlencoded'));
        $this->assertTrue($blocker->isAllowedContentType('text/html'));

        // test is allowing

        $blocker = new Blocker();
        $blocker->allowContentTypes([
            'application/json',
            'application/xml',
            'application/x-www-form-urlencoded',
        ]);

        $this->assertTrue($blocker->isAllowedContentType('application/json'));
        $this->assertTrue($blocker->isAllowedContentType('application/xml'));
        $this->assertTrue($blocker->isAllowedContentType('application/x-www-form-urlencoded'));
        $this->assertFalse($blocker->isAllowedContentType('text/html'));

        // test allow priority

        $blocker = new Blocker();
        $blocker->allowContentTypes([
            'application/json',
            'application/xml',
            'text/html',
        ]);
        $blocker->blockContentTypes([
            'text/html',
            'text/plain',
        ]);

        $this->assertTrue($blocker->isAllowedContentType('application/json'));
        $this->assertTrue($blocker->isAllowedContentType('application/xml'));
        $this->assertFalse($blocker->isAllowedContentType('application/x-www-form-urlencoded'));
        $this->assertFalse($blocker->isAllowedContentType('text/html'));
        $this->assertFalse($blocker->isAllowedContentType('text/plain'));
    }

    public function testHandleRequest(): void
    {
        $blocker = new Blocker();
        $blocker->blockHosts([
            'example.com',
        ]);

        $this->expectException(BlockedException::class);
        $blocker->handleRequest(new Request(
            'GET',
            'http://example.com'
        ));

        $request = new Request(
            'GET',
            'http://example.org'
        );

        $this->assertSame(
            $request,
            $blocker->handleRequest($request),
        );
    }
}
