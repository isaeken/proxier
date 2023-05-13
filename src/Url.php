<?php

namespace IsaEken\Proxier;

use IsaEken\Proxier\Exceptions\InvalidUrlException;

class Url
{
    public string|null $user = null;

    public string|null $pass = null;

    public string|null $scheme = null;

    public string|null $host = null;

    public int|null $port = null;

    public string|null $path = null;

    public string|null $query = null;

    public string|null $fragment = null;

    /**
     * @return string
     */
    public function make(): string
    {
        $url = empty($this->scheme) ? "http://" : $this->scheme . "://";
        $url .= empty($this->user) ? "" : $this->user;
        $url .= empty($this->pass) ? "" : (! empty($this->user) ? ":" : "") . $this->pass;
        $url .= empty($this->user) ? "" : "@";
        $url .= empty($this->host) ? "" : $this->host;
        $url .= empty($this->port) ? "" : ":" . $this->port;
        $url .= empty($this->path) ? "" : $this->path;
        $url .= empty($this->query) ? "" : "?" . $this->query;
        $url .= empty($this->fragment) ? "" : "#" . $this->fragment;

        return $url;
    }

    /**
     * @param string $url
     * @return Url
     * @throws InvalidUrlException
     */
    public static function parse(string $url): self
    {
        $instance = new self();
        $parsedUrl = parse_url($url);

        if (! filter_var($url, FILTER_VALIDATE_URL) || !$parsedUrl) {
            throw new Exceptions\InvalidUrlException($url);
        }

        $instance->scheme = $parsedUrl['scheme'] ?? null;
        $instance->user = $parsedUrl['user'] ?? null;
        $instance->pass = $parsedUrl['pass'] ?? null;
        $instance->host = $parsedUrl['host'] ?? null;
        $instance->port = $parsedUrl['port'] ?? null;
        $instance->path = ($parsedUrl['path']) ?? null;
        $instance->query = $parsedUrl['query'] ?? null;
        $instance->fragment = $parsedUrl['fragment'] ?? null;

        return $instance;
    }

    /**
     * @param string $service
     * @param string $host
     * @param string $url
     * @return string
     */
    public static function proxify(string $service, string $host, string $url): string
    {
        if (str_starts_with($url, '/')) {
            $url = $host . $url;
        }

        return "$service/?url=" . urlencode($url);
    }
}
