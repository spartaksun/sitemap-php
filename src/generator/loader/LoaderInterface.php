<?php

namespace spartaksun\sitemap\generator\loader;


use spartaksun\sitemap\generator\TriggerInterface;

interface LoaderInterface extends TriggerInterface
{
    public function load($url);
}