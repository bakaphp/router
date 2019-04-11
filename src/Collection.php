<?php

namespace Baka\Router;

use Phalcon\Mvc\Micro\Collection as PhCollection;

class Collection extends PhCollection
{
    final public static function fromRoute(Route $route): self
    {
        $collection = new self();
        $collection->setHandler($route->getHanlder(), true);

        return $collection;
    }
}
