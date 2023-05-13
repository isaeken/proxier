<?php

namespace IsaEken\Proxier\Features;

use GuzzleHttp\Psr7\Utils;
use IsaEken\Proxier\Config;
use Psr\Http\Message\ResponseInterface;

class ContentWriter extends Feature
{
    public string $header = '';

    public string $footer = '';

    public array $contentTypes = [
        'text/html',
    ];

    public function setHeader(string $header): self
    {
        $this->header = $header;
        return $this;
    }

    public function getHeader(): string
    {
        return $this->header;
    }

    public function setFooter(string $footer): self
    {
        $this->footer = $footer;
        return $this;
    }

    public function getFooter(): string
    {
        return $this->footer;
    }

    public function write(string $header, string $footer, string $html): string
    {
        return $header . $html . $footer;
    }

    public function boot(): void
    {
        $config = Config::getInstance()->get('content', []);
        $this->setHeader($config['header'] ?? '');
        $this->setFooter($config['footer'] ?? '');
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
        $newBody = $this->write(
            $this->getHeader(),
            $this->getFooter(),
            $body
        );
        $stream = Utils::streamFor($newBody, ['size' => mb_strlen($newBody, '8bit')]);

        /** @var ResponseInterface $response */
        $response = $response
            ->withBody($stream)
            ->withHeader('Content-Length', mb_strlen($newBody, '8bit'))
            ->withHeader('X-Original-Content-Length', $oldSize);

        return $response;
    }
}
