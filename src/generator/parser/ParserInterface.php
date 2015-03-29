<?php

namespace spartaksun\sitemap\generator\parser;


use spartaksun\sitemap\generator\TriggerInterface;

interface ParserInterface extends TriggerInterface
{
    public function getUrls($html);
}