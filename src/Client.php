<?php

namespace IsaEken\Proxier;

use GuzzleHttp\Psr7\Request;
use Psr\Http\Message\RequestInterface;

class Client implements Contracts\Client
{
    /**
     * User agent.
     *
     * @var string $userAgent
     */
    public string $userAgent = 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Proxier/1.0.0 Chrome/90.0.4430.212 Safari/537.36';

    /**
     * Client constructor.
     *
     * @param string $ip
     * @param RequestInterface $request
     */
    public function __construct(
        public string $ip,
        public RequestInterface $request,
    ) {
        // ...
    }

    /**
     * @inheritDoc
     */
    public function getIp(): string
    {
        return $this->ip;
    }

    /**
     * @inheritDoc
     */
    public function getRequest(): RequestInterface
    {
        return $this->request;
    }

    /**
     * @inheritDoc
     */
    public function getUserAgent(): string
    {
        return $this->userAgent;
    }

    /**
     * Create a new client.
     *
     * @param RequestInterface $request
     * @return static
     */
    public static function new(RequestInterface $request): static
    {
        return new static(
            $_SERVER['REMOTE_ADDR'] ?? '',
            $request
        );
    }

    /**
     * Create a new client from globals.
     *
     * @return static
     */
    public static function fromGlobals(): static
    {
        $method = $_SERVER['REQUEST_METHOD'] ?? '';
        $uri = $_SERVER['REQUEST_URI'] ?? '';
        $protocol = $_SERVER['SERVER_PROTOCOL'] ?? '';
        $headers = getallheaders();
        $body = file_get_contents('php://input');
        $version = explode('/', $protocol)[1] ?? '1.1';
        $request = new Request(
            $method,
            $uri,
            array_filter($headers, fn ($key) => ! in_array(strtolower($key), ['host', 'content-length', 'accept-encoding', 'accept'])),
            $body,
            $version,
        );

        return static::new($request);
    }
}
