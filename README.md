Sitemap generator (with parser).
=============================

## Example of usage
```php 
<?php
use spartaksun\sitemap\generator as generator;
```
### Choose type of storage:
```php
$storage = new generator\storage\MysqlStorage();
$storage->setKey('YOUR UNIQUE KEY FOR PROCESS');
```
### Choose type of loader and parser:
```php
$loader = new generator\loader\GuzzleLoader();
$parser = new generator\parser\HtmlParser();
```
### Initialize site processor:
```php
$processor = new generator\SiteProcessor($storage, $loader, $parser);
$generator = new generator\Generator($storage, $loader, $processor,
    new generator\writer\XmlWriter($storage)
);
```
### You may also process some events:
```php
$storage->on(
    generator\storage\UniqueValueStorageInterface::EVENT_ADD_URLS, function ($event) {
    /* @var generator\Event $event */
    $params = $event->getParams();
    // do something ...
});
$generator->siteProcessor->on(
    generator\SiteProcessor::EVENT_PROCESSED_ALL, function () {
    // do something ...
});
$generator->writer->on(
    generator\writer\WriterInterface::EVENT_FINISH, function () {
    // do something ...
});
```
### Then start generator:
```php
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
    
