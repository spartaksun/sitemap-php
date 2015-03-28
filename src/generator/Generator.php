<?php

namespace spartaksun\sitemap\generator;


use spartaksun\sitemap\generator\loader\LoaderInterface;
use spartaksun\sitemap\generator\storage\UniqueValueStorageInterface;

class Generator
{

    /**
     * @var storage\UniqueValueStorageInterface
     */
    public $storage;

    /**
     * @var loader\GuzzleLoader
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
     * @param LoaderInterface $loader
     * @param SiteWorker $worker
     * @param UniqueValueStorageInterface $storage
     */
    public function __construct(LoaderInterface $loader, SiteWorker $worker, UniqueValueStorageInterface $storage)
    {
        $this->loader = $loader;
        $this->worker = $worker;
        $this->storage = $storage;
    }

    /**
     * @param $startUrl
     * @throws \ErrorException
     */
    public function generate($startUrl)
    {
        $this->startUrl = $startUrl;
        $this->worker->run($this);
    }
}
