<?php

require_once __DIR__ . "/../../vendor/autoload.php";

use IsaEken\Proxier\Proxier;
use IsaEken\Proxier\Features;
use IsaEken\Proxier\Loggers;
use IsaEken\Proxier\Url;
use Psr\Http\Client\ClientExceptionInterface;

if (empty($_GET['url'])) {
    echo <<<HTML
<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Proxier</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="w-screen h-screen bg-gray-900 text-gray-50">
    <div class="w-screen h-screen flex flex-col justify-center items-center">
        <div class="w-1/2 flex flex-col justify-center items-center">
            <h1 class="text-4xl font-bold text-center mb-4">Proxier</h1>
            <form action="/" method="get" class="w-full flex flex-col justify-center items-center">
                <input name="url" type="text" class="w-full p-2 m-2 bg-gray-800 border-2 border-gray-700 rounded-lg placeholder-gray-500::placeholder" placeholder="URL" value="https://isaeken.com.tr" />
                <button class="w-full p-2 m-2 bg-gray-800 border-2 border-gray-700 rounded-lg hover:bg-gray-700" type="submit">Proxy</button>
            </form>
        </div>
    </div>
</body>
</html>
HTML;
    exit;
}

$accessLog = fopen(__DIR__ . '/../logs/access.log', 'a+');
$errorLog = fopen(__DIR__ . '/../logs/error.log', 'a+');

$serviceUrl = (empty($_SERVER['HTTPS']) ? 'http' : 'https') . "://$_SERVER[HTTP_HOST]";
$target = urldecode($_GET['url']);

$features = [
    Features\ReplaceUrls::new()->replaceUsing(function ($url) use ($serviceUrl, $target) {
        return Url::proxify($serviceUrl, $target, $url);
    }),
    Features\Blocker::new(),
    Features\ContentWriter::new(),
    Features\InjectScript::new(),
    Features\ReformatHeaders::new(),
];

$proxier = new Proxier();
$proxier->addFeature(...$features);
$proxier->setLogger(new Loggers\StreamLogger($accessLog, $errorLog));
$proxier->boot();

try {
    $response = $proxier->proxyUsingGlobals($target);
    foreach ($response->getHeaders() as $key => $value) {
        header($key . ": " . $value[0]);
    }

    echo $response->getBody()->getContents();
} catch (ClientExceptionInterface $e) {
    echo $e->getMessage();
}
