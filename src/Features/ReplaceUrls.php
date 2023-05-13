<?php

namespace IsaEken\Proxier\Features;

use DOMDocument;
use DOMXPath;
use GuzzleHttp\Psr7\Utils;
use Psr\Http\Message\ResponseInterface;

class ReplaceUrls extends Feature
{
    public array $contentTypes = [
        'text/html',
        'text/css',
        'text/javascript',
        'application/javascript',
        'application/x-javascript',
        'application/x-shockwave-flash',
        'application/json',
        'application/ld+json',
        'application/xml',
        'application/atom+xml',
        'application/rss+xml',
        'application/font-woff',
        'application/font-woff2',
        'application/vnd.ms-fontobject',
        'application/x-font-ttf',
        'application/x-font-opentype',
        'application/x-font-truetype',
        'application/x-font-woff',
        'application/x-font-woff2',
        'application/x-shockwave-flash',
        'application/xhtml+xml',
        'application/x-web-app-manifest+json',
    ];

    private mixed $replaceUsing = null;

    public function replaceDomUrls(string $haystack, callable $callback): string
    {
        $dom = new DOMDocument();
        @$dom->loadHTML($haystack);
        $xpath = new DOMXPath($dom);
        $nodes = $xpath->query('//@href|//@src');

        foreach ($nodes as $node) {
            $node->nodeValue = $callback($node->nodeValue);
        }

        return $dom->saveHTML();
    }

    public function replaceCssUrls(string $haystack, callable $callback): string
    {
        $callback = function (string $url) use ($callback) {
            if (str_starts_with($url, 'data:')) {
                return $url;
            }

            return $callback($url);
        };

        return preg_replace_callback(
            '/url\s*\(\s*[\'"]?+\/?+((?!http|vendor|website).+?)[\'"]?\s*\)/i',
            //fn (...$args) => $callback($args[0]['url']),
            fn (...$args) => "url('{$callback($args[0][1])}')",
            $haystack
        );
    }

    public function replaceUsing(callable $callable): static
    {
        $this->replaceUsing = $callable;
        return $this;
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
        $body = $this->replaceCssUrls($body, $this->replaceUsing);

        if (is_html($response)) {
            $body = $this->replaceDomUrls($body, $this->replaceUsing);
        }

        $stream = Utils::streamFor($body, ['size' => mb_strlen($body, '8bit')]);

        /** @var ResponseInterface $response */
        $response = $response
            ->withBody($stream)
            ->withHeader('Content-Length', mb_strlen($body, '8bit'));

        return $response;
    }
}
