<?php

namespace spartaksun\sitemap\generator\parser;


class HtmlParser
{

    /**
     * @var string
     */
    private $html;

    /**
     * @param $html
     * @throws ParserException
     */
    public function __construct($html)
    {
        if (empty($html) || !is_string($html)) {
            throw new ParserException('Empty html');
        }

        $this->html = $html;
    }

    /**
     * @return array
     * @throws ParserException
     */
    public function getUrls()
    {
        $dom = new \DOMDocument;

        libxml_use_internal_errors(true);
        $dom->loadHTML($this->html);

        $links = $dom->getElementsByTagName('a');
        /* @var $links \DOMElement[] */
        $urls = [];
        foreach ($links as $link) {
            $urls[] = $link->getAttribute('href');
        }

        return $urls;
    }
}