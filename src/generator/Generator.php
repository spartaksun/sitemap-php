<?php

namespace spartaksun\sitemap\generator;


use spartaksun\sitemap\generator\loader\LoaderInterface;
use spartaksun\sitemap\generator\storage\UniqueValueStorageInterface;

class Generator extends Object
{

    const ON_ADD_URLS = 'on_ad_urls';
    const ON_PARSE_FINISH = 'on_parse_finish';
    const ON_WRITE_FINISH = 'on_write_finish';


    /**
     * @var storage\UniqueValueStorageInterface
     */
    public $storage;

    /**
     * @var loader\GuzzleLoader
     */
    public $loader;

    /**
     * @var SiteProcessor
     */
    public $siteProcessor;

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
     * @param SiteProcessor $siteProcessor
     * @param UniqueValueStorageInterface $storage
     */
    public function __construct(UniqueValueStorageInterface $storage, LoaderInterface $loader,
        SiteProcessor $siteProcessor)
    {
        $this->loader           = $loader;
        $this->siteProcessor    = $siteProcessor;
        $this->storage          = $storage;
    }

    /**
     * @param $startUrl
     * @param $maxLevel
     */
    public function generate($startUrl, $maxLevel)
    {
        $this->startUrl = $startUrl;

        try {

            $this->storage->init();

            $this->siteProcessor->setMaxLevel($maxLevel);
            $this->siteProcessor->process($this->startUrl);


            $this->storage->deInit();

        } catch (loader\LoaderException $e) {
            echo $e->getMessage();
        } catch (parser\ParserException $e) {
            echo $e->getMessage();
        } finally {
            $this->storage->deInit();
        }
    }
}
