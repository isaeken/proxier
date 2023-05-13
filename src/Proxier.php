<?php

namespace IsaEken\Proxier;

use GuzzleHttp\Psr7\Uri;
use Psr\Http\Client\ClientExceptionInterface;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

class Proxier
{
    use Traits\HasLogger;
    use Traits\HasFeatures;
    use Traits\HasGuzzleClient;

    /**
     * Boot the proxier.
     *
     * @return void
     */
    public function boot(): void
    {
        $this->bootFeatures();
    }

    /**
     * Handle request.
     *
     * @param RequestInterface $request
     * @return RequestInterface
     */
    public function handleRequest(RequestInterface $request): RequestInterface
    {
        $current = $request;

        foreach ($this->getFeatures() as $feature) {
            $current = $feature->handleRequest($current);
        }

        return $current;
    }

    /**
     * Handle response.
     *
     * @param ResponseInterface $response
     * @return ResponseInterface
     */
    public function handleResponse(ResponseInterface $response): ResponseInterface
    {
        $current = $response;

        foreach ($this->getFeatures() as $feature) {
            $current = $feature->handleResponse($current);
        }

        return $current;
    }

    /**
     * Send request.
     *
     * @throws ClientExceptionInterface
     */
    public function request(RequestInterface $request): ResponseInterface
    {
        return $this->handleResponse(
            $this
                ->getGuzzleClient()
                ->sendRequest(
                    $this->handleRequest($request)
                ),
        );
    }

    /**
     * Proxy request.
     *
     * @throws ClientExceptionInterface
     */
    public function proxy(RequestInterface $request, Client|null $client = null): ResponseInterface
    {
        if ($client === null) {
            $client = Client::new($request);
        }

        $this->getLogger()->access($client, $request);
        return $this->request($request);
    }

    /**
     * Proxy request using globals.
     *
     * @throws ClientExceptionInterface
     */
    public function proxyUsingGlobals(string $uri): ResponseInterface
    {
        $client = Client::fromGlobals();
        $request = $client->getRequest();
        $request = $request->withUri(new Uri($uri));
        return $this->proxy($request);
    }
}
