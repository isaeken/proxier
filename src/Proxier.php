<?php

namespace IsaEken\Proxier;

class Proxier
{
    public string $header = <<<HEADER
<!-- isaeken/proxier -->
HEADER;

    public string $footer = <<<FOOTER
<!-- isaeken/proxier -->
FOOTER;

    public Converter $converter;

    public Html $html;

    public LoggerInterface|null $logger = null;

    public string $prefix;

    public string $url;

    public function getHeader(): string
    {
        return $this->header;
    }

    public function setHeader(string $header): Proxier
    {
        $this->header = $header;
        return $this;
    }

    public function getFooter(): string
    {
        return $this->footer;
    }

    public function setFooter(string $footer): Proxier
    {
        $this->footer = $footer;
        return $this;
    }

    public function getConverter(): Converter
    {
        if (!isset($this->converter)) {
            $converter = new Converter();
            $converter->setPrefix($this->getPrefix());
            $converter->setBase($this->getUrl());
            $this->setConverter($converter);
        }

        return $this->converter;
    }

    public function setConverter(Converter $converter): Proxier
    {
        $this->converter = $converter;
        return $this;
    }

    public function getHtml(): Html
    {
        if (!isset($this->html)) {
            $this->setHtml(new Html($this));
        }

        return $this->html;
    }

    public function setHtml(Html $html): Proxier
    {
        $this->html = $html;
        return $this;
    }

    public function getLogger(): LoggerInterface|null
    {
        return $this->logger;
    }

    public function setLogger(LoggerInterface|null $logger): Proxier
    {
        $this->logger = $logger;
        return $this;
    }

    public function getPrefix(): string
    {
        if (!isset($this->prefix)) {
            $scheme = isset($_SERVER['HTTPS']) ? 'https' : 'http';
            $port = $_SERVER["SERVER_PORT"] != 80 ? ":" . $_SERVER["SERVER_PORT"] : "";
            $this->setPrefix($scheme . "://" . $_SERVER["SERVER_NAME"] . $port . "/");
        }

        return $this->prefix;
    }

    public function setPrefix(string $prefix): Proxier
    {
        $this->prefix = $prefix;
        return $this;
    }

    public function getUrl(): string
    {
        if (!isset($this->url)) {
            $this->setUrl(substr($_SERVER["REQUEST_URI"], strlen($_SERVER["SCRIPT_NAME"]) + 1));
        }

        return $this->url;
    }

    public function setUrl(string $url): Proxier
    {
        if (str_starts_with($url, "//")) {
            $url = "http:" . $url;
        }

        if (!preg_match("@^.*://@", $url)) {
            $url = "http://" . $url;
        }

        $this->url = $url;
        return $this;
    }

    public function run(): void
    {
        if (! is_null($this->getLogger())) {
            $this->getLogger()->log($this->getUrl());
        }

        ob_start("ob_gzhandler");

        $response = Response::send($this->getUrl());

        $responseHeaderBlocks = array_filter(explode("\r\n\r\n", $response->headers));
        $lastHeaderBlock = end($responseHeaderBlocks);
        $headerLines = explode("\r\n", $lastHeaderBlock);

        foreach ($headerLines as $header) {
            if (stripos($header, "Content-Length") === false && stripos($header, "Transfer-Encoding") === false) {
                header($header);
            }
        }

        $contentType = $response->contentType;

        if (stripos($contentType, "text/html") !== false) {
            echo $this->getHtml()->parse($response->body);
        } else if (stripos($contentType, "text/css") !== false) {
            echo $this->getConverter()->css($response->body);
        } else {
            echo $response->body;
        }
    }
}
