<?php

namespace IsaEken\Proxier\Tests;

use IsaEken\Proxier\Exceptions\InvalidUrlException;
use IsaEken\Proxier\Url;
use PHPUnit\Framework\TestCase;

class UrlTest extends TestCase
{
    public function testParse(): void
    {
        $urls = [
            'https://www.google.com/search?q=hello+world&hl=en' => [
                'scheme' => 'https',
                'user' => null,
                'pass' => null,
                'host' => 'www.google.com',
                'port' => null,
                'path' => '/search',
                'query' => 'q=hello+world&hl=en',
                'fragment' => null,
            ],
            'https://www.google.com/search?q=hello+world&hl=en#test' => [
                'scheme' => 'https',
                'user' => null,
                'pass' => null,
                'host' => 'www.google.com',
                'port' => null,
                'path' => '/search',
                'query' => 'q=hello+world&hl=en',
                'fragment' => 'test',
            ],
            'https://www.google.com:8080/search?q=hello+world&hl=en#test' => [
                'scheme' => 'https',
                'user' => null,
                'pass' => null,
                'host' => 'www.google.com',
                'port' => 8080,
                'path' => '/search',
                'query' => 'q=hello+world&hl=en',
                'fragment' => 'test',
            ],
            'tcp://www.google.com:8080/search?q=hello+world&hl=en#test' => [
                'scheme' => 'tcp',
                'user' => null,
                'pass' => null,
                'host' => 'www.google.com',
                'port' => 8080,
                'path' => '/search',
                'query' => 'q=hello+world&hl=en',
                'fragment' => 'test',
            ],
            'https://user@www.google.com:8080/search?q=hello+world&hl=en#test' => [
                'scheme' => 'https',
                'user' => 'user',
                'pass' => null,
                'host' => 'www.google.com',
                'port' => 8080,
                'path' => '/search',
                'query' => 'q=hello+world&hl=en',
                'fragment' => 'test',
            ],
            'https://user:pass@www.google.com:8080/search?q=hello+world&hl=en#test' => [
                'scheme' => 'https',
                'user' => 'user',
                'pass' => 'pass',
                'host' => 'www.google.com',
                'port' => 8080,
                'path' => '/search',
                'query' => 'q=hello+world&hl=en',
                'fragment' => 'test',
            ],
        ];

        foreach ($urls as $url => $parsed) {
            $model = new Url();
            foreach ($parsed as $key => $value) {
                $model->$key = $value;
            }

            $this->assertEquals(
                Url::parse($url),
                $model,
            );
        }

        $this->expectException(InvalidUrlException::class);
        Url::parse('invalid url');
    }

    public function testMakeUrl(): void
    {
        $url = new Url();
        $url->scheme = 'tcp';
        $url->user = 'user';
        $url->pass = 'pass';
        $url->host = 'www.google.com';
        $url->port = 8080;
        $url->path = '/search';
        $url->query = 'q=hello+world&hl=en';
        $url->fragment = 'test';
        $this->assertEquals(
            'tcp://user:pass@www.google.com:8080/search?q=hello+world&hl=en#test',
            $url->make(),
        );
    }
}
