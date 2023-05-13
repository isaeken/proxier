<?php

namespace IsaEken\Proxier\Loggers;

use IsaEken\Proxier\Contracts\Client;
use IsaEken\Proxier\Contracts\Logger;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Throwable;

/**
 * Class ArrayLogger
 *
 * Collect logs to an array. High memory usage and for debugging, testing or advanced usage.
 */
class ArrayLogger implements Logger
{
    private array $accessLogs = [];

    private array $errorLogs = [];

    /**
     * @return string[]
     */
    public function getAccessLogs(): array
    {
        return $this->accessLogs;
    }

    /**
     * @return string[]
     */
    public function getErrorLogs(): array
    {
        return $this->errorLogs;
    }

    public function access(Client $client, RequestInterface $request, ResponseInterface|null $response = null): void
    {
        $message = sprintf('[ACCESS] %s %s %s %s %s', $client->getIp(), $request->getMethod(), $request->getUri(), $response?->getStatusCode() ?? 0, $response?->getReasonPhrase() ?? '');
        $this->accessLogs[] = $message;
    }

    public function error(Client $client, RequestInterface $request, ResponseInterface|null $response = null, ?Throwable $exception = null): void
    {
        $message = sprintf('[ERROR] %s %s %s %s %s %s', $client->getIp(), $request->getMethod(), $request->getUri(), $response?->getStatusCode() ?? 0, $response?->getReasonPhrase() ?? '', $exception?->getMessage() ?? '');
        $this->errorLogs[] = $message;
    }
}
