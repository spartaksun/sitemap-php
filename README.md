# sitemap-php
Sitemap generator. Parses html pages to find internal site links
=============================

## Usage
```php 
<?php
use spartaksun\sitemap\generator as generator;
```
```php
// choose type of storage:
$storage = new generator\storage\MysqlStorage();
$storage->setKey('YOUR UNIQUE KEY FOR PROCESS');
```

```php
// choose type of loader and parser:
$loader = new generator\loader\GuzzleLoader();
$parser = new generator\parser\HtmlParser();
```

```php
// initialize site processor:
$processor = new generator\SiteProcessor($storage, $loader, $parser);
$generator = new generator\Generator($storage, $loader, $processor,
    new generator\writer\XmlWriter($storage)
);
```

```php
// you may also process some events:
$storage->on(generator\storage\UniqueValueStorageInterface::EVENT_ADD_URLS, function ($event) {
    /* @var generator\Event $event */
    $params = $event->getParams();
    // do something ...
});
$generator->siteProcessor->on(generator\SiteProcessor::EVENT_PROCESSED_ALL, function () {
    // do something ...
});
$generator->writer->on(generator\writer\WriterInterface::EVENT_FINISH, function () {
    // do something ...
});
```

```php
// start generator:
try {
    $generator->generate(
        'http://site.ru' /* main site page */,
        3 /* nesting level */,
        '/path/to/save.zip' /* path to save */
    );
} catch (generator\GeneratorException $e) {
    // do something ...
} catch (\Exception $e) {
    // do something ...
} finally {
    $storage->deInit();
}
```
    
