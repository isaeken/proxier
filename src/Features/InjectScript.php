<?php

namespace IsaEken\Proxier\Features;

use GuzzleHttp\Psr7\Utils;
use IsaEken\Proxier\Config;
use Psr\Http\Message\ResponseInterface;

class InjectScript extends Feature
{
    public array $contentTypes = [
        'text/html',
    ];

    public string $script = '';

    public function useScript(string $script): self
    {
        $this->script = $script;
        return $this;
    }

    public function inject(string $script, string $html): string
    {
        $script = '<script>' . $script . '</script>';
        return preg_replace('/<\/body>/', $script . '</body>', $html);
    }

    public function boot(): void
    {
        $config = Config::getInstance()->get('inject', []);
        $this->useScript($config['script'] ?? '');
    }

    /**
     * @inheritDoc
     */
    public function handleResponse(ResponseInterface $response): ResponseInterface
    {
        if (! in_array(get_content_type($response), $this->contentTypes)) {
            return $response;
        }

        $body = $response->getBody()->getContents();
        $oldSize = $response->getBody()->getSize();
        $newBody = $this->inject(
            $this->script,
            $body
        );

        /** @var ResponseInterface $response */
        $response = $response
            ->withBody(Utils::streamFor($newBody, ['size' => mb_strlen($newBody, '8bit')]))
            ->withHeader('Content-Length', mb_strlen($newBody, '8bit'))
            ->withHeader('X-Original-Content-Length', $oldSize);

        return $response;
    }
}
