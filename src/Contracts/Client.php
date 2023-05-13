<?php

namespace IsaEken\Proxier\Contracts;

use Psr\Http\Message\RequestInterface;

interface Client
{
    /**
     * Return client ip address.
     *
     * @return string
     */
    public function getIp(): string;

    /**
     * Return client request.
     *
     * @return RequestInterface
     */
    public function getRequest(): RequestInterface;

    /**
     * Return client user agent.
     *
     * @return string
     */
    public function getUserAgent(): string;
}
