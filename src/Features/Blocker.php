<?php

namespace IsaEken\Proxier\Features;

use IsaEken\Proxier\Config;
use IsaEken\Proxier\Exceptions\BlockedException;
use Psr\Http\Message\RequestInterface;

class Blocker extends Feature
{
    public array|null $allowedHosts = null;

    public array|null $allowedMethods = null;

    public array|null $allowedContentTypes = null;

    public array|null $blockedHosts = null;

    public array|null $blockedMethods = null;

    public array|null $blockedContentTypes = null;

    public function isAllowed(string $type, string|array $values): bool
    {
        $type = ucfirst($type);
        $values = is_string($values) ? [$values] : $values;
        $allows = "allowed$type";
        $blocks = "blocked$type";

        $allowedIsNull = empty($this->$allows);
        $blockedIsNull = empty($this->$blocks);

        if ($allowedIsNull && $blockedIsNull) {
            return true;
        }

        if (! $blockedIsNull) {
            foreach ($this->$blocks as $block) {
                foreach ($values as $value) {
                    if (fnmatch($block, $value)) {
                        return false;
                    }
                }
            }
        }

        if (! $allowedIsNull) {
            foreach ($this->$allows as $allow) {
                foreach ($values as $value) {
                    if (fnmatch($allow, $value)) {
                        return true;
                    }
                }
            }

            return false;
        }

        return true;
    }

    public function isAllowedHosts(string ...$hosts): bool
    {
        $hosts = array_map('strtolower', $hosts);
        return $this->isAllowed('hosts', $hosts);
    }

    public function isAllowedMethod(string ...$methods): bool
    {
        $methods = array_map('strtoupper', $methods);
        return $this->isAllowed('methods', $methods);
    }

    public function isAllowedContentType(string ...$contentTypes): bool
    {
        $contentTypes = array_map('strtolower', $contentTypes);
        return $this->isAllowed('contentTypes', $contentTypes);
    }

    public function allowHosts(array $hosts): Blocker
    {
        $hosts = array_map('strtolower', $hosts);
        $this->allowedHosts = $hosts;
        return $this;
    }

    public function allowMethods(array $methods): Blocker
    {
        $methods = array_map('strtoupper', $methods);
        $this->allowedMethods = $methods;
        return $this;
    }

    public function allowContentTypes(array $contentTypes): Blocker
    {
        $contentTypes = array_map('strtolower', $contentTypes);
        $this->allowedContentTypes = $contentTypes;
        return $this;
    }

    public function blockHosts(array $hosts): Blocker
    {
        $hosts = array_map('strtolower', $hosts);
        $this->blockedHosts = $hosts;
        return $this;
    }

    public function blockMethods(array $methods): Blocker
    {
        $methods = array_map('strtoupper', $methods);
        $this->blockedMethods = $methods;
        return $this;
    }

    public function blockContentTypes(array $contentTypes): Blocker
    {
        $contentTypes = array_map('strtolower', $contentTypes);
        $this->blockedContentTypes = $contentTypes;
        return $this;
    }

    public function boot(): void
    {
        $config = Config::getInstance()->get('blocker', []);
        $this->allowedHosts = $config['allowed_hosts'] ?? null;
        $this->allowedMethods = $config['allowed_methods'] ?? null;
        $this->allowedContentTypes = $config['allowed_content_types'] ?? null;
        $this->blockedHosts = $config['blocked_hosts'] ?? null;
        $this->blockedMethods = $config['blocked_methods'] ?? null;
        $this->blockedContentTypes = $config['blocked_content_types'] ?? null;
    }

    /**
     * @inheritDoc
     * @throws BlockedException
     */
    public function handleRequest(RequestInterface $request): RequestInterface
    {
        $host = $request->getUri()->getHost();
        $method = $request->getMethod();
        $contentType = $request->getHeaderLine('Content-Type');

        if (! $this->isAllowedHosts($host)) {
            throw BlockedException::create($request, "Host \"$host\" is blocked.");
        }

        if (! $this->isAllowedMethod($method)) {
            throw BlockedException::create($request, "Method \"$method\" is blocked.");
        }

        if (! $this->isAllowedContentType($contentType)) {
            throw BlockedException::create($request, "Content-Type \"$contentType\" is blocked.");
        }

        return $request;
    }
}
