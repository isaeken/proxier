<?php

namespace IsaEken\Proxier\Loggers;

use IsaEken\Proxier\Contracts\Client;
use IsaEken\Proxier\Contracts\Logger;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Throwable;

/**
 * Class NullLogger
 *
 * Null logger. No logs will be saved.
 */
class NullLogger implements Logger
{
    public function access(Client $client, RequestInterface $request, ResponseInterface|null $response = null): void
    {
    }

    public function error(Client $client, RequestInterface $request, ResponseInterface|null $response = null, ?Throwable $exception = null): void
    {
    }
}
