<?php

namespace IsaEken\Proxier\Exceptions;

use Exception;
use Psr\Http\Message\RequestInterface;

class BlockedException extends Exception
{
    /**
     * Create a new exception instance.
     *
     * @param RequestInterface $request
     * @param string $reason
     * @return static
     */
    public static function create(RequestInterface $request, string $reason): self
    {
        return new self("The url \"{$request->getUri()}\" is blocked. Reason: $reason");
    }
}
