<?php

namespace spartaksun\sitemap\generator\parser;


class HtmlParser
{

    /**
     * @return array
     * @throws ParserException
     */
    public function getUrls($html)
    {
        if (empty($html) || !is_string($html)) {
            throw new ParserException('Empty html');
        }

        $dom = new \DOMDocument;

        libxml_use_internal_errors(true);
        $dom->loadHTML($html);

        $links = $dom->getElementsByTagName('a');
        /* @var $links \DOMElement[] */
        $urls = [];
        foreach ($links as $link) {
            $urls[] = $link->getAttribute('href');
        }

        return $urls;
    }
}