<?php

namespace IsaEken\Proxier\Contracts;

use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Throwable;

interface Logger
{
    /**
     * Access log.
     *
     * @param Client $client
     * @param RequestInterface $request
     * @param ResponseInterface|null $response
     * @return void
     */
    public function access(Client $client, RequestInterface $request, ResponseInterface|null $response = null): void;

    /**
     * Error log.
     *
     * @param Client $client
     * @param RequestInterface $request
     * @param ResponseInterface|null $response
     * @param Throwable|null $exception
     * @return void
     */
    public function error(Client $client, RequestInterface $request, ResponseInterface|null $response = null, Throwable|null $exception = null): void;
}
