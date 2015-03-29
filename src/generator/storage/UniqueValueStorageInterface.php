<?php

namespace spartaksun\sitemap\generator\storage;


use spartaksun\sitemap\generator\TriggerInterface;

interface UniqueValueStorageInterface extends TriggerInterface
{

    const EVENT_ADD_URLS = 'on_add_urls';


    /**
     * @param $storageKey
     */
    public function setKey($storageKey);

    /**
     * Initialize storage
     * @throws StorageException
     * @return void
     */
    public function init();

    /**
     * Destroy storage
     * @throws StorageException
     * @return void
     */
    public function deInit();

    /**
     * @param array $values
     * @throws StorageException
     * @return array of newly added urls
     */
    public function add(array $values, $level);

    /**
     * Number of storage elements
     * @return integer
     */
    public function total();

    /**
     * Unique values. Use limit and offset
     * @return array
     */
    public function get();

    /**
     * Apply limit to result
     * @param integer $limit
     * @throws StorageException
     * @return bool
     */
    public function setLimit($limit);

    /**
     * Apply offset to result
     * @param $offset
     * @throws \InvalidArgumentException
     * @return mixed
     */
    public function setOffset($offset);

}