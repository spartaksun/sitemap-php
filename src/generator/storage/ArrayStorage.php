<?php

namespace spartaksun\sitemap\generator\storage;


class ArrayStorage implements UniqueValueStorage
{

    /**
     * Keeps array of values
     * @var array
     */
    private $storage = [];
    /**
     * @var int
     */
    private $offset = 0;
    /**
     * @var int|null
     */
    private $limit;
    /**
     * @var int
     */
    private $total = 0;


    /**
     * @inheritdoc
     */
    public function add($value)
    {
        if(!is_string($value)) {
            throw new \InvalidArgumentException('You have only add string values');
        }

        if(isset($this->storage[$value])) {
            return false;
        }
        $this->total = array_push($this->storage, $value);

        return true;
    }

    /**
     * @inheritdoc
     */
    public function get()
    {
        return array_slice($this->storage, $this->offset, is_null($this->limit) ? $this->total - 1 : $this->limit);
    }

    /**
     * @inheritdoc
     */
    public function limit($limit)
    {
        if($limit <= 0) {
            throw new \InvalidArgumentException('You should set limit to more than 0.');
        }

        $this->limit = $limit;
    }

    /**
     * @inheritdoc
     */
    public function offset($offset)
    {
        if($offset <= 0) {
            throw new \InvalidArgumentException('You should set offset to more than 0.');
        }

        $this->offset = $offset;
    }

    /**
     * @inheritdoc
     */
    public function total()
    {
        return $this->total;
    }
}