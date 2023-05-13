<?php

namespace IsaEken\Proxier\Contracts;

use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

interface Feature
{
    /**
     * Boot feature.
     *
     * @return void
     */
    public function boot(): void;

    /**
     * Handle request and return new request.
     *
     * @param RequestInterface $request
     * @return RequestInterface
     */
    public function handleRequest(RequestInterface $request): RequestInterface;

    /**
     * Handle response and return new response.
     *
     * @param ResponseInterface $response
     * @return ResponseInterface
     */
    public function handleResponse(ResponseInterface $response): ResponseInterface;
}
