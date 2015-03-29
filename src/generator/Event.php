<?php

namespace spartaksun\sitemap\generator;


class Event
{

    private $params = [];


    function __construct(array $params = [])
    {
        $this->params = $params;
    }

    /**
     * @return array
     */
    public function getParams()
    {
        return $this->params;
    }

    /**
     * @param array $params
     */
    public function setParams(array $params)
    {
        $this->params = $params;
    }


}