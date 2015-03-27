<?php

namespace spartaksun\sitemap\generator;


use spartaksun\sitemap\generator\storage\UniqueValueStorage;

class SiteWorker
{
    /**
     * @var Generator
     */
    private $generator;

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

            $this->processAll([$this->mainPage], 2);

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

    protected function processLevel(array $urls)
    {
        $levelUrlsTotal = [];

        foreach($urls as $url) {
            foreach($this->processUrl($url) as $p) {
                array_push($levelUrlsTotal, $p);
            }
        }

        return $levelUrlsTotal;
    }

}