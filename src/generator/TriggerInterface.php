<?php

namespace spartaksun\sitemap\generator;


interface TriggerInterface
{
    public function on($name, \Closure $callback);
    public function trigger($name, Event $event);
}