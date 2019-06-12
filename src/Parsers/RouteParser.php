<?php

namespace Baka\Router\Parsers;

use Baka\Router\Utils\Http;
use Baka\Router\Route;
use Baka\Router\Collection;
use function in_array;

class RouteParser
{
    const ACTIONS = [
        Http::POST => 'create',
        Http::GET => 'index',
        Http::PUT => 'edit',
        Http::PATCH => 'edit',
        Http::DELETE => 'delete',
    ];

    protected $route;

    public function __construct(Route $route)
    {
        $this->route = $route;   
    }

    public function parse()
    {
        $collections = [];
        $this->hasMethod(Http::POST) and array_push($collections,...$this->getPostCollection());
        $this->hasMethod(Http::GET) and array_push($collections,...$this->getGetCollections());
        $this->hasMethod(Http::PUT) and array_push($collections,...$this->getPutCollection());
        $this->hasMethod(Http::PATCH) and array_push($collections,...$this->getPatchCollection());
        $this->hasMethod(Http::DELETE) and array_push($collections,...$this->getDeleteCollection());
        
        return $collections;
    }

    protected function getPostCollection(): array
    {
        $collection = Collection::fromRoute($this->route);

        $action = $this->route->getAction() ?? static::ACTIONS[Http::POST];

        $collection->post(
            $this->route->getPattern(),
            $action
        );

        $collections[] = $collection;

        return $collections;
    }
    protected function getGetCollections(): array
    {
        $collection = Collection::fromRoute($this->route);
        $collection2 = clone $collection;

        $action =  $this->route->getAction() ?? static::ACTIONS[Http::GET];

        $collection->get(
            $this->route->getPattern(),
            $action
        );

        $collection2->get(
            $this->route->getPattern().'/{id:[0-9]+}', // TODO: Find a name to use a constant
            $action
        );

        $collections[] = $collection;
        $collections[] = $collection2;

        return $collections;
    }
    protected function getPutCollection(): array
    {
        $collection = Collection::fromRoute($this->route);

        $action = $this->route->getAction() ?? static::ACTIONS[Http::PUT];

        $collection->put(
            $this->route->getPattern().'/{id:[0-9]+}',
            $action
        );

        $collections[] = $collection;

        return $collections;
    }
    protected function getPatchCollection(): array
    {
        $collection = Collection::fromRoute($this->route);

        $action =  $this->route->getAction() ?? static::ACTIONS[Http::PATCH];

        $collection->patch(
            $this->route->getPattern().'/{id:[0-9]+}',
            $action
        );

        $collections[] = $collection;

        return $collections;
    }
    protected function getDeleteCollection(): array
    {
        $collection = Collection::fromRoute($this->route);
        
        $action = $this->route->getAction() ?? static::ACTIONS[Http::DELETE];

        $collection->delete(
            $this->route->getPattern().'/{id:[0-9]+}',
            $action
        );

        $collections[] = $collection;

        return $collections;
    }

    protected function hasMethod(string $method): bool
    {
        return in_array($method, $this->route->getVia());
    }
}
