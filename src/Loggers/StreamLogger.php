<?php

namespace IsaEken\Proxier\Loggers;

use IsaEken\Proxier\Contracts\Client;
use IsaEken\Proxier\Contracts\Logger;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Throwable;

/**
 * Class StreamLogger
 *
 * Write logs to a stream. Low memory usage and for production usage.
 */
class StreamLogger implements Logger
{
    public function __construct(
        protected mixed $accessStream = 'php://stdout',
        protected mixed $errorStream = 'php://stderr',
    ) {
        //
    }

    public function getAccessStream(): mixed
    {
        return $this->accessStream;
    }

    public function setAccessStream(mixed $accessStream): self
    {
        $this->accessStream = $accessStream;
        return $this;
    }

    public function getErrorStream(): mixed
    {
        return $this->errorStream;
    }

    public function setErrorStream(mixed $errorStream): self
    {
        $this->errorStream = $errorStream;
        return $this;
    }

    public function write(string $message, mixed $stream): void
    {
        if (is_string($stream)) {
            $stream = fopen($stream, 'w');
        }

        $message = '[' . date('Y-m-d H:i:s') . '] ' . $message . PHP_EOL;
        fwrite($stream, $message);
    }

    public function access(Client $client, RequestInterface $request, ResponseInterface|null $response = null): void
    {
        $ip = $client->getIp();
        $method = $request->getMethod();
        $uri = $request->getUri();
        $statusCode = $response?->getStatusCode() ?? 0;
        $reasonPhrase = $response?->getReasonPhrase() ?? '';
        $message = sprintf('[ACCESS] %s %s %s %s %s', $ip, $method, $uri, $statusCode, $reasonPhrase);
        $this->write($message, $this->getAccessStream());
    }

    public function error(Client $client, RequestInterface $request, ResponseInterface|null $response = null, ?Throwable $exception = null): void
    {
        $ip = $client->getIp();
        $method = $request->getMethod();
        $uri = $request->getUri();
        $statusCode = $response?->getStatusCode() ?? 0;
        $reasonPhrase = $response?->getReasonPhrase() ?? '';
        $exceptionMessage = $exception?->getMessage() ?? '';
        $message = sprintf('[ERROR] %s %s %s %s %s %s', $ip, $method, $uri, $statusCode, $reasonPhrase, $exceptionMessage);
        $this->write($message, $this->getErrorStream());
    }
}
