<?php

namespace spartaksun\sitemap\generator\writer;


use spartaksun\sitemap\generator\TriggerInterface;

interface WriterInterface extends TriggerInterface
{
    const EVENT_FINISH = 'finish';

    public function write($startUrl, $filePath);
}