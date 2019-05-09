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
        $this->hasMethod(Http::POST) and $collections = $this->addPostCollection($collections);
        $this->hasMethod(Http::GET) and $collections = $this->addGetCollections($collections);
        $this->hasMethod(Http::PUT) and $collections = $this->addPutCollection($collections);
        $this->hasMethod(Http::PATCH) and $collections = $this->addPatchCollection($collections);
        $this->hasMethod(Http::DELETE) and $collections = $this->addDeleteCollection($collections);
        
        return $collections;
    }

    protected function addPostCollection(array $collections): array
    {
        $collection = Collection::fromRoute($this->route);

        $action = $this->route->getAction() ?? static::ACTIONS[Http::POST];

        $collection->post(
            $this->route->getPattern(),
            "{$action}Action"
        );

        $collections[] = $collection;

        return $collections;
    }
    protected function addGetCollections(array $collections): array
    {
        $collection = Collection::fromRoute($this->route);
        $collection2 = clone $collection;

        $action =  $this->route->getAction() ?? static::ACTIONS[Http::GET];

        $collection->get(
            $this->route->getPattern(),
            "{$action}Action"
        );

        $collection2->get(
            $this->route->getPattern().'/{id:[0-9]+}',
            "{$action}Action"
        );

        $collections[] = $collection;
        $collections[] = $collection2;

        return $collections;
    }
    protected function addPutCollection(array $collections): array
    {
        $collection = Collection::fromRoute($this->route);

        $action = $this->route->getAction() ?? static::ACTIONS[Http::PUT];

        $collection->put(
            $this->route->getPattern().'/{id:[0-9]+}',
            "{$action}Action"
        );

        $collections[] = $collection;

        return $collections;
    }
    protected function addPatchCollection(array $collections): array
    {
        $collection = Collection::fromRoute($this->route);

        $action =  $this->route->getAction() ?? static::ACTIONS[Http::PATCH];

        $collection->patch(
            $this->route->getPattern().'/{id:[0-9]+}',
            "{$action}Action"
        );

        $collections[] = $collection;

        return $collections;
    }
    protected function addDeleteCollection(array $collections): array
    {
        $collection = Collection::fromRoute($this->route);
        
        $action = $this->route->getAction() ?? static::ACTIONS[Http::DELETE];

        $collection->delete(
            $this->route->getPattern().'/{id:[0-9]+}',
            "{$action}Action"
        );

        $collections[] = $collection;

        return $collections;
    }

    protected function hasMethod(string $method): bool
    {
        return in_array($method, $this->route->getVia());
    }
}
