<?php

namespace spartaksun\sitemap\generator;


use spartaksun\sitemap\generator\loader\LoaderException;

class SiteWorker
{
    /**
     * @var Generator
     */
    private $generator;

    /**
     * @var string url of main page
     */
    private $mainPage;


    /**
     * @param Generator $generator
     */
    public function __construct(Generator $generator)
    {
        $this->generator = $generator;
    }

    /**
     * Runs processing of site
     * @param $startUrl
     * @throws GeneratorException
     */
    public function run($startUrl)
    {
        try {
            $this->mainPage = UrlHelper::getMainPageUrl($startUrl);

            $generator = $this->generator;
            $storage = $generator->storage;
            $storage->init();

            $this->processAll([$this->mainPage], 1);

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

            $levelResult = $this->processLevel($levelResult);
            $currentLevel++;

        } while (!empty($levelResult) || (!is_null($maxLevel) && $currentLevel <= $maxLevel));

    }

    /**
     * @param $url
     * @throws LoaderException
     * @return array
     */
    protected function processUrl($url)
    {
        $generator = $this->generator;

        $parser = new parser\HtmlParser(
            $generator->loader->load($url)
        );

        $normalizedUrls = UrlHelper::normalizeUrls(
            $parser->getUrls(),
            $this->mainPage,
            $url
        );

        return $generator->storage
            ->add($normalizedUrls);

    }

    /**
     * @param array $urls
     * @return array
     */
    protected function processLevel(array $urls)
    {
        $levelUrlsTotal = [];

        foreach($urls as $url) {
            try {
                foreach($this->processUrl($url) as $p) {
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