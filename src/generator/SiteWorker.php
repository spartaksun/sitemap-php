<?php

namespace spartaksun\sitemap\generator;


class SiteWorker
{
    /**
     * @var Generator
     */
    private $generator;


    /**
     * @param Generator $generator
     */
    function __construct(Generator $generator)
    {
        $this->generator = $generator;
    }

    /**
     * Runs processing of site
     * @param $url
     * @throws GeneratorException
     */
    public function run($url)
    {
        try {
            $generator = $this->generator;

            $parser = new parser\HtmlParser(
                $generator->loader->load($url)
            );

            $normalizedUrls = UrlHelper::normalizeUrls(
                $parser->getUrls(),
                UrlHelper::getMainPageUrl($url)
            );

            $storage = $generator->storage;
            $storage->init();
            $storage->add($normalizedUrls);


            $total = $storage->total();
            $storage->setOffset(5);
            $storage->setLimit(100);

            echo $total;
            var_dump($storage->get());

        } catch (loader\LoaderException $e) {
            echo $e->getMessage();
        } catch (parser\ParserException $e) {
            echo $e->getMessage();
        }
    }

}