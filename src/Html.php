<?php

namespace IsaEken\Proxier;

use DOMDocument;
use DOMXPath;

readonly class Html
{
    public function __construct(public Proxier $proxier)
    {
        // ...
    }

    public function getScriptContent(): string
    {
        return file_get_contents(__DIR__ . '/../resources/script.js');
    }

    public function parse(string $body): string
    {
        $document = new DomDocument();
        @$document->loadHTML($body);
        $xpath = new DOMXPath($document);

        foreach ($xpath->query('//form') as $form) {
            $method = $form->getAttribute("method");
            $action = $form->getAttribute("action");
            $action = empty($action) ? $this->proxier->getUrl() : $this->proxier->getConverter()->rel2abs($action);
            $form->setAttribute("action", $this->proxier->getPrefix() . $action);
        }

        foreach ($xpath->query('//style') as $style) {
            $style->nodeValue = $this->proxier->getConverter()->css($style->nodeValue);
        }

        foreach ($xpath->query('//*[@style]') as $element) {
            $element->setAttribute("style", $this->proxier->getConverter()->css($element->getAttribute("style")));
        }

        $attributes = ["href", "src"];

        foreach ($attributes as $attribute) {
            foreach ($xpath->query('//*[@' . $attribute . ']') as $element) {
                $attributeContent = $element->getAttribute($attribute);

                if ($attribute == "href" && (stripos($attributeContent, "javascript:") === 0 || stripos($attributeContent, "mailto:") === 0)) {
                    continue;
                }

                $attributeContent = $this->proxier->getConverter()->rel2abs($attributeContent);
                $attributeContent = $this->proxier->getPrefix() . $attributeContent;
                $element->setAttribute($attribute, $attributeContent);
            }
        }

        $head = $xpath->query('//head')->item(0);
        $body = $xpath->query('//body')->item(0);
        $html = $xpath->query('//html')->item(0);
        $prependElement = $head ?? ($body ?? $html);

        if (! is_null($prependElement)) {
            $content = $this->getScriptContent();
            $content = str_replace("{{url}}", $this->proxier->getUrl(), $content);
            $content = str_replace("{{prefix}}", $this->proxier->getPrefix(), $content);

            $scriptElement = $document->createElement("script", $content);
            $scriptElement->setAttribute("type", "text/javascript");
            $prependElement->appendChild($scriptElement);
        }

        return $this->proxier->getHeader() . $document->saveHTML() . $this->proxier->getFooter();
    }
}
