<?php

namespace spartaksun\sitemap\generator\writer;


use Sitemap\Collection;
use Sitemap\Formatter\XML\SitemapIndex;
use Sitemap\Formatter\XML\URLSet;
use Sitemap\Sitemap\SitemapEntry;
use spartaksun\sitemap\generator\GeneratorException;
use spartaksun\sitemap\generator\Object;
use spartaksun\sitemap\generator\storage\UniqueValueStorageInterface;

class XmlWriter extends Object implements WriterInterface
{
    /**
     * @var UniqueValueStorageInterface
     */
    private $storage;

    /**
     * @var int
     */
    public $maxLinksPerFile = 50000;

    /**
     * @var int
     */
    private $currentNum = 0;

    /**
     * @var array
     */
    private $index = [];

    /**
     * @var Collection
     */
    private $currentCollection;


    /**
     * @param UniqueValueStorageInterface $storage
     */
    public function __construct(UniqueValueStorageInterface $storage)
    {
        $this->storage = $storage;
        mb_internal_encoding('utf-8');
    }

    /**
     * @param string $startUrl
     * @param string $filePath
     * @throws GeneratorException
     */
    public function write($startUrl, $filePath)
    {
        $storage = $this->storage;

        $zip = new \ZipArchive();
        if ($zip->open($filePath, \ZipArchive::CREATE) !== true) {
            throw new GeneratorException('Can not open archive');
        }

        $offset = 0;
        $limit = 500;
        $now = date('Y-m-d');

        $this->currentCollection = new Collection();

        $i = 0;
        while (true) {

            $storage->setOffset($offset);
            $storage->setLimit($limit);

            $links = $storage->get();

            if (empty($links)) {
                $this->saveCollection($zip, $i);
                break;
            }

            foreach ($links as $url) {
                $this->currentNum++;

                $map = new SitemapEntry($url->url);
                $map->setPriority(round(1 / $url->level, 2));
                $map->setChangeFreq(SitemapEntry::CHANGEFREQ_ALWAYS);
                $map->setLastMod($now);

                if ($this->currentNum == $this->maxLinksPerFile) {
                    $this->saveCollection($zip, $i);
                }

                $this->currentCollection->addSitemap($map);
            }

            $offset += $limit;
        }


        $indexCollection = new Collection();
        $indexCollection->setFormatter(new SitemapIndex());

        $baseUrl = rtrim($startUrl, chr('/')) . '/';
        foreach ($this->index as $fileName) {
            $map = new SitemapEntry($baseUrl . $fileName);
            $map->setLastMod($now);
            $indexCollection->addSitemap($map);
        }

        $zip->addFromString("sitemap-index.xml", $indexCollection->output());

        if ($zip->close()) {
            $this->trigger(self::EVENT_FINISH);
        } else {
            throw new GeneratorException('Cant save sitemap archive');
        }
    }

    /**
     * @param $zip
     * @param $i
     */
    public function saveCollection(\ZipArchive $zip, & $i)
    {
        $this->currentNum = 0;

        $file = "sitemap-{$i}.xml";
        $this->index[] = $file;

        $this->currentCollection->setFormatter(new URLSet());
        $zip->addFromString($file, $this->currentCollection->output());
        $this->currentCollection = new Collection();

        $i++;
    }

}