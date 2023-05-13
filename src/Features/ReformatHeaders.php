<?php

namespace IsaEken\Proxier\Features;

use Psr\Http\Message\ResponseInterface;

class ReformatHeaders extends Feature
{
    public array $ignoredHeaders = [
        'transfer-encoding',
        'host',
        'location',
        'content-length',
    ];

    /**
     * @inheritDoc
     */
    public function handleResponse(ResponseInterface $response): ResponseInterface
    {
        foreach ($this->ignoredHeaders as $ignoredHeader) {
            $response = $response->withoutHeader($ignoredHeader);
        }

        return $response;
    }
}
