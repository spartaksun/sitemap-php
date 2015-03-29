<?php

namespace spartaksun\sitemap\generator;


class Url
{

    public $url;
    public $level;

    public function __construct($row)
    {
        $this->url = $row['url'];
        $this->level = $row['level'];
    }
}