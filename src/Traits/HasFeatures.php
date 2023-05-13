<?php

namespace IsaEken\Proxier\Traits;

use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;
use IsaEken\Proxier\Contracts\Feature;

trait HasFeatures
{
    private array $features = [];

    /**
     * Get all features.
     *
     * @return Feature[]
     */
    public function getFeatures(): array
    {
        return $this->features;
    }

    /**
     * Add feature(s).
     *
     * @param Feature ...$feature
     * @return $this
     */
    public function addFeature(Feature ...$feature): static
    {
        foreach ($feature as $item) {
            $this->features[$item::class] = $item;
        }

        return $this;
    }

    /**
     * Get feature.
     *
     * @param string $feature
     * @return Feature|null
     */
    public function getFeature(string $feature): Feature|null
    {
        return $this->features[$feature] ?? null;
    }

    /**
     * Check feature exists.
     *
     * @param string $feature
     * @return bool
     */
    public function hasFeature(string $feature): bool
    {
        return isset($this->features[$feature]);
    }

    /**
     * Remove feature.
     *
     * @param string $feature
     * @return $this
     */
    public function removeFeature(string $feature): static
    {
        unset($this->features[$feature]);
        return $this;
    }

    /**
     * Remove all features.
     *
     * @return $this
     */
    public function removeAllFeatures(): static
    {
        $this->features = [];
        return $this;
    }

    /**
     * Boot features.
     *
     * @return $this
     */
    public function bootFeatures(): static
    {
        foreach ($this->features as $feature) {
            $feature->boot();
        }

        return $this;
    }

    /**
     * Handle request using features.
     *
     * @param Request $request
     * @return Request
     */
    public function handleRequestFeatures(Request $request): Request
    {
        $current = $request;

        /** @var Feature $feature */
        foreach ($this->features as $feature) {
            $current = $feature->handleRequest($current);
        }

        return $current;
    }

    /**
     * Handle response using features.
     *
     * @param Response $response
     * @return Response
     */
    public function handleResponseFeatures(Response $response): Response
    {
        $current = $response;

        /** @var Feature $feature */
        foreach ($this->features as $feature) {
            $current = $feature->handleResponse($current);
        }

        return $current;
    }
}
