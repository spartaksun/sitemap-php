<?php

namespace spartaksun\sitemap\generator;


class Generator
{
    /**
     * @param $url
     * @throws \ErrorException
     */
    public function generate($url)
    {
        $worker = new SiteWorker($url);
        $worker->run();
    }

}