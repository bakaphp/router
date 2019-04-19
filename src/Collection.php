<?php

namespace Baka\Router;

use Phalcon\Mvc\Micro\Collection as PhCollection;
use Phalcon\Utils\Slug;
use Baka\Router\Parser\MiddlewareParser;
use Baka\Support\Arr;

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

    public function getMiddlewares() : array
    {
        $middlewares = [
            Middleware::BEFORE => [],
            Middleware::AFTER => []
        ];

        foreach ($this->route->getMiddlewares() as $notation) {
            $middlewareParser = new MiddlewareParser($notation);
            $middleware = $middlewareParser->parse();
            $middlewares[$middleware->getEvent()][] = $middleware;
        }

        return [$this->getCollectionIdentifier() => $middlewares];
    }

    protected function getCollectionIdentifier() : string
    {
        return Slug::generate(
            $this->getHandler().'-'.$this->getHandlers()[0][2]
        );
    }
}
