<?php

require_once __DIR__ . "/../vendor/autoload.php";

use IsaEken\Proxier\LoggerInterface;
use IsaEken\Proxier\Proxier;

$proxier = new Proxier();

class Logger implements LoggerInterface {
    public function log(string $url): void
    {
        $ip = $_SERVER['REMOTE_ADDR'];
        $date = date("d.m.Y");
        $time = date("H:i:s");
        $filename = __DIR__ . "/logs/$date-$ip.log";
        $content = file_get_contents($filename);
        $content .= "[$time] $url\n";
        file_put_contents($filename, $content);
    }
}

$proxier->setLogger(new Logger());

$url = "http://isaeken.com.tr";
$uri = $_SERVER['REQUEST_URI'];

if (strlen($uri) > 3) {
    if (str_starts_with($uri, "/")) {
        $uri = substr($uri, strlen("/"));
    }

    if (! (str_starts_with($uri, "http:") || str_starts_with($uri, "https:")) || str_starts_with($uri, "//")) {
        $uri = "$url/$uri";
    }

    $proxier->setUrl($uri);
} else {
    $proxier->setUrl($url);
}

$proxier->setHeader(<<<HTML
<!-- isaeken/proxier -->
<div style="position:fixed; top:0; left: 0; right:0; background: white; z-index: 99999999999; box-shadow: 0 0 10px rgba(0,0,0,0.5); padding: 10px; font-family: sans-serif; font-size: 12px;">
    <div style="display: flex; justify-content: space-between; align-items: center;">
        <div style="display: flex; align-items: center;">
            <span style="font-weight: bold; font-size: 16px;">isaeken/proxier</span>
        </div>
    </div>
</div>
<div style="margin: 60px"></div>
HTML
);

$proxier->run();
