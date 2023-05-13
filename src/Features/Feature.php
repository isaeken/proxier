<?php

namespace IsaEken\Proxier\Features;

use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

abstract class Feature implements \IsaEken\Proxier\Contracts\Feature
{
    public function boot(): void
    {
        // ...
    }

    /**
     * @inheritDoc
     */
    public function handleRequest(RequestInterface $request): RequestInterface
    {
        return $request;
    }

    /**
     * @inheritDoc
     */
    public function handleResponse(ResponseInterface $response): ResponseInterface
    {
        return $response;
    }

    public static function new(): static
    {
        return new static();
    }
}
