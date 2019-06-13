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

    /**
     * Contructor.
     *
     * @param Route $route
     */
    public function __construct(Route $route)
    {
        $this->route = $route;
    }

    /**
     * Parse the route to create collection.
     *
     * @return void
     */
    public function parse()
    {
        $collections = [];
        $this->hasMethod(Http::POST) and array_push($collections, ...$this->getPostCollection());
        $this->hasMethod(Http::GET) and array_push($collections, ...$this->getGetCollections());
        $this->hasMethod(Http::PUT) and array_push($collections, ...$this->getPutCollection());
        $this->hasMethod(Http::PATCH) and array_push($collections, ...$this->getPatchCollection());
        $this->hasMethod(Http::DELETE) and array_push($collections, ...$this->getDeleteCollection());

        return $collections;
    }

    /**
     * Given the route we will add add additional handles if needed
     * right now it will only be for groups.
     *
     * @param Route $route
     * @return string|null
     */
    public function groupRouteHandle(Route $route): ?string
    {
        return $route->isGroup() ? '/{id}' : null;
    }

    /**
     * Get the route parse POST collection.
     *
     * @return array
     */
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

    /**
     * Get the route parse GET collection.
     *
     * @return array
     */
    protected function getGetCollections(): array
    {
        $collection = Collection::fromRoute($this->route);
        $collection2 = clone $collection;

        $action = $this->route->getAction() ?? static::ACTIONS[Http::GET];

        $collection->get(
            $this->route->getPattern(),
            $action
        );

        $collection2->get(
            $this->route->getPattern() . $this->groupRouteHandle($this->route), // TODO: Find a name to use a constant
            'getById' // TODO: Find a better way to achieve this
        );

        $collections[] = $collection;
        $collections[] = $collection2;

        return $collections;
    }

    /**
     * Get the route parse PUT collection.
     *
     * @return array
     */
    protected function getPutCollection(): array
    {
        $collection = Collection::fromRoute($this->route);

        $action = $this->route->getAction() ?? static::ACTIONS[Http::PUT];

        $collection->put(
            $this->route->getPattern() . $this->groupRouteHandle($this->route),
            $action
        );

        $collections[] = $collection;

        return $collections;
    }

    /**
     * Get the route parse PATCH collection.
     *
     * @return array
     */
    protected function getPatchCollection(): array
    {
        $collection = Collection::fromRoute($this->route);

        $action = $this->route->getAction() ?? static::ACTIONS[Http::PATCH];

        $collection->patch(
            $this->route->getPattern() . $this->groupRouteHandle($this->route),
            $action
        );

        $collections[] = $collection;

        return $collections;
    }

    /**
     * Get the route parse DELETE collection.
     *
     * @return array
     */
    protected function getDeleteCollection(): array
    {
        $collection = Collection::fromRoute($this->route);

        $action = $this->route->getAction() ?? static::ACTIONS[Http::DELETE];

        $collection->delete(
            $this->route->getPattern() . $this->groupRouteHandle($this->route),
            $action
        );

        $collections[] = $collection;

        return $collections;
    }

    /**
     * Verify if it has a method.
     *
     * @return bool
     */
    protected function hasMethod(string $method): bool
    {
        return in_array($method, $this->route->getVia());
    }
}
