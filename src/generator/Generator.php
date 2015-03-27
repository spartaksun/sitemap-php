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
     * @var SiteWorker
     */
    public $worker;

    /**
     * @var string URL
     */
    public $startUrl;

    /**
     * @var int nested level
     */
    public $level = 1;

    /**
     * @param $startUrl
     */
    public function __construct($startUrl)
    {
        $this->startUrl = $startUrl;
    }

    /**
     * @throws \ErrorException
     */
    public function generate()
    {
        $this->worker->run($this);
    }
}
