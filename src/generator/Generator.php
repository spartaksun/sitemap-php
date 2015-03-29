<?php

namespace spartaksun\sitemap\generator;


use spartaksun\sitemap\generator\loader\LoaderInterface;
use spartaksun\sitemap\generator\storage\UniqueValueStorageInterface;
use spartaksun\sitemap\generator\writer\WriterInterface;
use spartaksun\sitemap\generator\writer\XmlWriter;

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
     * @var WriterInterface
     */
    public $writer;

    /**
     * @var string URL
     */
    public $startUrl;

    /**
     * @var int nested level
     */
    public $level = 1;



    /**
     * @param UniqueValueStorageInterface $storage
     * @param LoaderInterface $loader
     * @param SiteProcessor $siteProcessor
     * @param WriterInterface $writer
     */
    public function __construct(UniqueValueStorageInterface $storage, LoaderInterface $loader,
        SiteProcessor $siteProcessor, WriterInterface $writer)
    {
        $this->loader           = $loader;
        $this->siteProcessor    = $siteProcessor;
        $this->storage          = $storage;
        $this->writer           = $writer;

    }

    /**
     * @param $startUrl
     * @param $maxLevel
     * @param $filePath
     */
    public function generate($startUrl, $maxLevel, $filePath)
    {
        $this->startUrl = $startUrl;

        try {

            $this->storage->init();

            $this->siteProcessor->setMaxLevel($maxLevel);
            $this->siteProcessor->process($startUrl);
            $this->writer->write($startUrl, $filePath);

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
