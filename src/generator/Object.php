<?php

namespace spartaksun\sitemap\generator;


class Object implements TriggerInterface
{

    /**
     * @var array
     */
    private $events = [];

    public function on($name, \Closure $callback)
    {
        $this->events[$name][] = $callback;
    }

    public function trigger($name, Event $event = null)
    {
        if(!empty($this->events[$name])) {
            foreach($this->events[$name] as $callback) {
                call_user_func($callback, $event);
            }
        }
    }

}