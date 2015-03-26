<?php

namespace spartaksun\sitemap\generator;


use spartaksun\sitemap\generator\storage\ArrayStorage;

class SiteWorker
{
    /**
     * @var string
     */
    private $url;

    /**
     * @param $url
     * @throws GeneratorException
     */
    function __construct($url)
    {
        if (empty($url) || !is_string($url)) {
            throw new GeneratorException('Empty html');
        }

        $this->url = $url;
    }

    /**
     * Runs processing of site
     */
    public function run()
    {
        try {

            $loader = new loader\Loader($this->url);
            $parser = new parser\HtmlParser(
                $loader->load()
            );

            $normalizedUrls = UrlHelper::normalizeUrls(
                $parser->getUrls(),
                UrlHelper::getMainPageUrl($this->url)
            );

            $storage = new ArrayStorage();
            foreach ($normalizedUrls as $url) {
                $storage->add($url);
            }

            $total = $storage->total();
            $storage->offset(5);

            echo $total;
            var_dump($storage->get());

        } catch (loader\LoaderException $e) {
            echo $e->getMessage();
        } catch (parser\ParserException $e) {
            echo $e->getMessage();
        }
    }

}