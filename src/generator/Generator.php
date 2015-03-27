<?php

namespace spartaksun\sitemap\generator;


class Generator
{

    /**
     * @var storage\UniqueValueStorage
     */
    public $storage;

    /**
     * @var loader\Loader
     */
    public $loader;

    /**
     * @param $startUrl
     * @throws \ErrorException
     */
    public function generate($startUrl)
    {
        $worker = new SiteWorker($this);
        $worker->run($startUrl);
    }
}
