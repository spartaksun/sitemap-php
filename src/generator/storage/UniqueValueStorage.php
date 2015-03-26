<?php

namespace spartaksun\sitemap\generator\storage;


interface UniqueValueStorage
{
    /**
     * @param string $value
     * @throws \InvalidArgumentException
     * @return bool false if value was already added to storage
     */
    public function add($value);

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
     * @throws \InvalidArgumentException
     * @return bool
     */
    public function limit($limit);

    /**
     * Apply offset to result
     * @param $offset
     * @throws \InvalidArgumentException
     * @return mixed
     */
    public function offset($offset);

}