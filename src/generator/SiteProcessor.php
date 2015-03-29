<?php

namespace spartaksun\sitemap\generator;


use spartaksun\sitemap\generator\loader\LoaderInterface;
use spartaksun\sitemap\generator\storage\UniqueValueStorageInterface;

class SiteProcessor extends Object
{

    const EVENT_PROCESSED_ALL = 'processed_all';

    /**
     * @var parser\HtmlParser
     */
    public $parser;

    /**
     * @var string url of main page
     */
    private $mainPage;

    /**
     * @var UniqueValueStorageInterface
     */
    private $storage;

    /**
     * @var LoaderInterface
     */
    private $loader;

    /**
     * @var int
     */
    private $maxLevel = 1;


    /**
     * @param UniqueValueStorageInterface $storage
     * @param LoaderInterface $loader
     * @param parser\ParserInterface $parser
     */
    public function __construct(UniqueValueStorageInterface $storage, LoaderInterface $loader,
        parser\ParserInterface $parser )
    {
        $this->parser  = $parser;
        $this->storage = $storage;
        $this->loader  = $loader;
    }

    /**
     * Runs processing of site
     * @param $startUrl
     */
    public function process($startUrl)
    {
        $this->mainPage = UrlHelper::getMainPageUrl($startUrl);
        $this->processAllUrls([$this->mainPage]);

        $this->trigger(self::EVENT_PROCESSED_ALL);
    }

    /**
     * @param array $levelResult
     */
    private function processAllUrls(array $levelResult)
    {
        $currentLevel = 1;
        do {

            $levelResult = $this->processLevel($levelResult, $currentLevel);
            $currentLevel++;

            if(!is_null($this->maxLevel) && $currentLevel >= $this->maxLevel) {
                break;
            }

        } while (!empty($levelResult));

    }

    /**
     * @param $url
     * @param $currentLevel
     * @throws loader\LoaderException
     * @return array
     */
    private function processUrl($url, $currentLevel)
    {
        $html = $this->loader->load($url);

        $normalizedUrls = UrlHelper::normalizeUrls(
            $this->parser->getUrls($html),
            $this->mainPage,
            $url
        );

        return $this->storage
            ->add($normalizedUrls, $currentLevel);

    }

    /**
     * @param array $urls
     * @return array
     */
    private function processLevel(array $urls, $currentLevel)
    {
        $levelUrlsTotal = [];

        foreach($urls as $url) {
            try {
                foreach($this->processUrl($url, $currentLevel) as $p) {
                    array_push($levelUrlsTotal, $p);
                }
            } catch (loader\LoaderException $e) {
                // TODO mark URL as failed
                continue;
            }

        }

        return $levelUrlsTotal;
    }

    /**
     * @param int $maxLevel
     */
    public function setMaxLevel($maxLevel)
    {
        $this->maxLevel = $maxLevel;
    }


}