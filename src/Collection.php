<?php

namespace Baka\Router;

use Phalcon\Mvc\Micro\Collection as PhCollection;

class Collection extends PhCollection
{
    /**
     * Create a new instance of Collection based on Route instance
     *
     * @param Route $route
     *
     * @return self
     */
    final public static function fromRoute(Route $route): self
    {
        $collection = new self();
        $collection->setHandler($route->getHanlder(), true);

        return $collection;
    }
}
