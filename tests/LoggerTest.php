<?php

namespace IsaEken\Proxier\Tests;

use Exception;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;
use IsaEken\Proxier\Client;
use IsaEken\Proxier\Loggers\ArrayLogger;
use IsaEken\Proxier\Loggers\StreamLogger;
use PHPUnit\Framework\TestCase;

class LoggerTest extends TestCase
{
    public function testArrayLogger(): void
    {
        $logger = new ArrayLogger();

        $request = new Request('GET', 'https://example.com');
        $response = new Response(200, [], 'Hello World!');
        $client = new Client('127.0.0.1', $request);

        $logger->access($client, $request, $response);
        $this->assertContains('[ACCESS] 127.0.0.1 GET https://example.com 200 OK', $logger->getAccessLogs());

        $logger->error($client, $request, $response, new Exception('Test Error'));
        $this->assertContains('[ERROR] 127.0.0.1 GET https://example.com 200 OK Test Error', $logger->getErrorLogs());
    }

    public function testAccessStream(): void
    {
        $request = new Request('GET', 'https://example.com');
        $response = new Response(200, [], 'Hello World!');
        $client = new Client('127.0.01', $request);

        $stream = fopen('php://memory', 'w+');
        $logger = new StreamLogger(
            $stream,
            $stream,
        );
        $logger->access($client, $request, $response);
        rewind($stream);
        $this->assertStringContainsString('[ACCESS] 127.0.01 GET https://example.com 200 ', stream_get_contents($stream));
    }

    public function testErrorStream(): void
    {
        $request = new Request('GET', 'https://example.com');
        $response = new Response(200, [], 'Hello World!');
        $client = new Client('127.0.01', $request);

        $stream = fopen('php://memory', 'w+');
        $logger = new StreamLogger(
            $stream,
            $stream,
        );
        $logger->error($client, $request, $response, new Exception('Test Error'));
        rewind($stream);
        $this->assertStringContainsString('[ERROR] 127.0.01 GET https://example.com 200 OK Test Error', stream_get_contents($stream));
    }
}
