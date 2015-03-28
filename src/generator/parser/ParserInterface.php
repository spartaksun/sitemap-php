<?php

namespace spartaksun\sitemap\generator\parser;


interface ParserInterface
{
    public function getUrls($html);
}