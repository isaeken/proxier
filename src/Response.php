<?php

namespace IsaEken\Proxier;

class Response
{
    public static string $userAgent = "Mozilla/5.0 (compatible; isaeken/proxier)";

    public function __construct(
        public readonly mixed  $responseInfo,
        public readonly string $headers,
        public readonly string $contentType,
        public readonly string $body,
    )
    {
        // ...
    }

    public static function send(string $url): static
    {
        $userAgent = empty($_SERVER["HTTP_USER_AGENT"]) ? static::$userAgent : $_SERVER["HTTP_USER_AGENT"];

        $curl = curl_init();
        curl_setopt($curl, CURLOPT_USERAGENT, $userAgent);

        $browserRequestHeaders = getallheaders();

        if (is_array($browserRequestHeaders)) {
            unset($browserRequestHeaders["Host"]);
            unset($browserRequestHeaders["Content-Length"]);
            unset($browserRequestHeaders["Accept-Encoding"]);
            unset($browserRequestHeaders["Aceept"]);
        }

        curl_setopt($curl, CURLOPT_ENCODING, "");

        $curlRequestHeaders = [];
        foreach ($browserRequestHeaders as $name => $value) {
            $curlRequestHeaders[] = $name . ": " . $value;
        }

        curl_setopt($curl, CURLOPT_HTTPHEADER, $curlRequestHeaders);


        switch ($_SERVER["REQUEST_METHOD"]) {
            case "GET":
                $getData = [];

                foreach ($_GET as $key => $value) {
                    $getData[] = urlencode($key) . "=" . urlencode($value);
                }

                if (count($getData) > 0) {
                    $url = substr($url, 0, strrpos($url, "?"));
                    $url .= "?" . implode("&", $getData);
                }

                break;

            case "POST":
                curl_setopt($curl, CURLOPT_POST, true);
                curl_setopt($curl, CURLOPT_POSTFIELDS, file_get_contents("php://input"));
                break;

            case "PUT":
                curl_setopt($curl, CURLOPT_PUT, true);
                curl_setopt($curl, CURLOPT_INFILE, fopen("php://input"));
                break;
        }

        curl_setopt($curl, CURLOPT_HEADER, true);
        curl_setopt($curl, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_FAILONERROR, true);
        curl_setopt($curl, CURLOPT_URL, $url);

        $response = curl_exec($curl);
        $responseInfo = curl_getinfo($curl);
        $headerSize = curl_getinfo($curl, CURLINFO_HEADER_SIZE);
        $contentType = curl_getinfo($curl, CURLINFO_CONTENT_TYPE);

        curl_close($curl);

        $responseHeaders = substr($response, 0, $headerSize);
        $responseBody = substr($response, $headerSize);

        return new static(
            responseInfo: $responseInfo,
            headers: $responseHeaders,
            contentType: $contentType,
            body: $responseBody,
        );
    }
}
