<?php

namespace IsaEken\Proxier;

class Converter
{
    public string $prefix;

    public string $base;

    public function getPrefix(): string
    {
        return $this->prefix;
    }

    public function setPrefix(string $prefix): Converter
    {
        $this->prefix = $prefix;
        return $this;
    }

    public function getBase(): string
    {
        return $this->base;
    }

    public function setBase(string $base): Converter
    {
        $this->base = $base;
        return $this;
    }

    public function rel2abs(string|null $rel): string
    {
        $rel = empty($rel) ? "." : $rel;

        if (parse_url($rel, PHP_URL_SCHEME) != "" || str_starts_with($rel, "//")) {
            return $rel;
        }

        if ($rel[0] == "#" || $rel[0] == "?") {
            return $this->getBase() . $rel;
        }

        extract(parse_url($this->getBase()));

        $path = isset($path) ? preg_replace('#/[^/]*$#', "", $path) : "/";

        if ($rel[0] == '/') {
            $path = "";
        }

        $port = isset($port) && $port != 80 ? ":" . $port : "";
        $auth = "";

        if (isset($user)) {
            $auth = $user;
            if (isset($pass)) {
                $auth .= ":" . $pass;
            }
            $auth .= "@";
        }

        $abs = "$auth$host$path$port/$rel";

        for ($n = 1; $n > 0; $abs = preg_replace(array("#(/\.?/)#", "#/(?!\.\.)[^/]+/\.\./#"), "/", $abs, -1, $n)) {
        }

        return $scheme . "://" . $abs;
    }

    public function css(string $css): string
    {
        return preg_replace_callback(
            '/url\((.*?)\)/i',
            function ($matches) {
                $url = $matches[1];

                if (str_starts_with($url, "'")) {
                    $url = trim($url, "'");
                }

                if (str_starts_with($url, "\"")) {
                    $url = trim($url, "\"");
                }

                if (stripos($url, "data:") === 0) {
                    return "url(" . $url . ")";
                }

                return "url(" . $this->getPrefix() . $this->rel2abs($url) . ")";
            },
            $css
        );
    }
}
