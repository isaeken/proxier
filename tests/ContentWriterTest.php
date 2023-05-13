<?php

namespace IsaEken\Proxier\Tests;

use GuzzleHttp\Psr7\Response;
use IsaEken\Proxier\Features\ContentWriter;
use PHPUnit\Framework\TestCase;

class ContentWriterTest extends TestCase
{
    public function testEmpty(): void
    {
        $contentWriter = new ContentWriter();
        $response = new Response(
            200,
            [],
            'Hello World!'
        );

        $this->assertEquals(
            'Hello World!',
            $contentWriter->handleResponse($response)->getBody()->getContents()
        );
    }

    public function testHeader(): void
    {
        $contentWriter = new ContentWriter();
        $contentWriter->setHeader('Header');
        $response = new Response(
            200,
            [
                'Content-Type' => 'text/html',
            ],
            'Hello World!'
        );

        $this->assertEquals(
            'HeaderHello World!',
            $contentWriter->handleResponse($response)->getBody()->getContents()
        );
    }

    public function testFooter(): void
    {
        $contentWriter = new ContentWriter();
        $contentWriter->setFooter('Footer');
        $response = new Response(
            200,
            [
                'Content-Type' => 'text/html',
            ],
            'Hello World!'
        );

        $this->assertEquals(
            'Hello World!Footer',
            $contentWriter->handleResponse($response)->getBody()->getContents()
        );
    }

    public function testBoth(): void
    {
        $contentWriter = new ContentWriter();
        $contentWriter->setHeader('Header');
        $contentWriter->setFooter('Footer');
        $response = new Response(
            200,
            [
                'Content-Type' => 'text/html',
            ],
            'Hello World!'
        );

        $this->assertEquals(
            'HeaderHello World!Footer',
            $contentWriter->handleResponse($response)->getBody()->getContents()
        );
    }
}
