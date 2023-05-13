<?php

namespace IsaEken\Proxier\Exceptions;

use Exception;

class InvalidUrlException extends Exception
{
    /**
     * Create a new exception instance.
     *
     * @param string $url
     * @return static
     */
    public static function create(string $url): self
    {
        return new self("The url \"$url\" is invalid.");
    }
}
