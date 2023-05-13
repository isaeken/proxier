<?php

use Psr\Http\Message\ResponseInterface;

if (! function_exists('get_content_type')) {
    /**
     * Get content type from response.
     *
     * @param ResponseInterface $response
     * @return string
     */
    function get_content_type(ResponseInterface $response): string
    {
        $contentType = $response->getHeaderLine('Content-Type');
        return explode(';', $contentType)[0];
    }
}

if (! function_exists('is_html')) {
    /**
     * Check if response is html.
     *
     * @param ResponseInterface $response
     * @return bool
     */
    function is_html(ResponseInterface $response): bool
    {
        return in_array(get_content_type($response), [
            'text/html',
            'application/xhtml+xml',
        ]);
    }
}
