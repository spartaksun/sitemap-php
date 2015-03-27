<?php

namespace spartaksun\sitemap\generator\storage;


interface UniqueValueStorage
{

    /**
     * @param $storageKey
     */
    public function __construct($storageKey);

    /**
     * Initialize storage
     * @throws StorageException
     * @return void
     */
    public function init();

    /**
     * @param array $values
     * @throws StorageException
     * @return bool false if value was already added to storage
     */
    public function add(array $values);

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