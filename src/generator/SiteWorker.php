<?php

namespace spartaksun\sitemap\generator;


use spartaksun\sitemap\generator\loader\LoaderException;
use spartaksun\sitemap\generator\parser\ParserInterface;

class SiteWorker
{

    /**
     * @var parser\HtmlParser
     */
    public $parser;

    /**
     * @var Generator
     */
    private $generator;

    /**
     * @var string url of main page
     */
    private $mainPage;


    /**
     * @param ParserInterface $parser
     */
    public function __construct(ParserInterface $parser)
    {
        $this->parser = $parser;
    }

    /**
     * Runs processing of site
     * @param Generator $generator
     * @throws GeneratorException
     */
    public function run(Generator $generator)
    {
        $this->generator = $generator;

        try {
            $this->mainPage = UrlHelper::getMainPageUrl($generator->startUrl);

            $storage = $generator->storage;
            $storage->init();

            $this->processAll([$this->mainPage], $generator->level);

            $storage->setOffset(0);
            $storage->setLimit(10000);

            echo $storage->total();
            var_dump($storage->get());

        } catch (loader\LoaderException $e) {
            echo $e->getMessage();
        } catch (parser\ParserException $e) {
            echo $e->getMessage();
        }
    }

    /**
     * @param array $levelResult
     * @param null $maxLevel
     */
    protected function processAll(array $levelResult, $maxLevel = null)
    {
        $currentLevel = 1;
        do {

            $levelResult = $this->processLevel($levelResult, $currentLevel);
            $currentLevel++;

            if(!is_null($maxLevel) && $currentLevel >= $maxLevel) {
                break;
            }

        } while (!empty($levelResult));

    }

    /**
     * @param $url
     * @throws LoaderException
     * @return array
     */
    protected function processUrl($url, $currentLevel)
    {
        $html = $this->generator->loader->load($url);

        $normalizedUrls = UrlHelper::normalizeUrls(
            $this->parser->getUrls($html),
            $this->mainPage,
            $url
        );

        return $this->generator->storage
            ->add($normalizedUrls, $currentLevel);

    }

    /**
     * @param array $urls
     * @return array
     */
    protected function processLevel(array $urls, $currentLevel)
    {
        $levelUrlsTotal = [];

        foreach($urls as $url) {
            try {
                foreach($this->processUrl($url, $currentLevel) as $p) {
                    array_push($levelUrlsTotal, $p);
                }
            } catch (LoaderException $e) {
                // TODO mark URL as failed
                continue;
            }

        }

        return $levelUrlsTotal;
    }

}