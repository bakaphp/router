<?php

namespace Baka\Router;

use Phalcon\Mvc\Micro\Collection as PhCollection;
use Phalcon\Utils\Slug;
use Baka\Router\Parsers\MiddlewareParser;

class Collection extends PhCollection
{
    protected $route;

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
        $collection->route = $route;
        $collection->setHandler($route->getHanlder(), true);

        return $collection;
    }

    /**
     * Return collection's middlewares
     *
     * @return array
     */
    public function getMiddlewares() : array
    {
        $middlewares = [
           
        ];

        foreach ($this->route->getMiddlewares() as $notation) {
            $middlewareParser = new MiddlewareParser($notation);
            $middleware = $middlewareParser->parse();
            $middlewares[] = $middleware;
        }

        return $middlewares;
    }

    /**
     * Return a unique identifier for the current collection
     *
     * @return string
     */
    public function getCollectionIdentifier() : string
    {
        return Slug::generate(
            $this->getHandler().'-'.$this->getHandlers()[0][2]
        );
    }
}
