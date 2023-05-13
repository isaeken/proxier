<?php

namespace IsaEken\Proxier\Traits;

use GuzzleHttp\Client;
use IsaEken\Proxier\Config;

trait HasGuzzleClient
{
    /**
     * @var Client|null
     */
    public Client|null $guzzleClient = null;

    /**
     * Create default guzzle client.
     *
     * @return Client
     */
    public function createDefaultGuzzleClient(): Client
    {
        return new Client(Config::getInstance()->get('guzzle', []));
    }

    /**
     * Get guzzle client.
     *
     * @return Client
     */
    public function getGuzzleClient(): Client
    {
        if (! isset($this->guzzleClient)) {
            $this->guzzleClient = $this->createDefaultGuzzleClient();
        }

        return $this->guzzleClient;
    }

    /**
     * Set guzzle client.
     *
     * @param Client $guzzleClient
     * @return static
     */
    public function setGuzzleClient(Client $guzzleClient): static
    {
        $this->guzzleClient = $guzzleClient;
        return $this;
    }
}
