<?php

namespace Baka\Router;

use Baka\Support\Arr;
use InvalidArgumentException;
use function array_merge;

class RouteGroup
{
    protected $defaultPrefix;
    protected $defaultNamespace;
    protected $defaultAction;
    protected $routes = [];
    protected $middlewares = [];

    public function __construct(array $routes)
    {
        $this->routes = $routes;
    }

    public static function from(array $routes): self
    {
        return new self($routes);
    }

    public function addRoute(Route $route): self
    {
        $this->routes[] = $route;

        return $this;
    }

    public function addMiddlewares(...$middlewares): self
    {
        $this->middlewares = array_merge($this->middlewares, $middlewares);

        return $this;
    }

    public function defaultNamespace(string $defaultNamespace): self
    {
        $this->defaultNamespace = $defaultNamespace;

        return $this;
    }

    public function defaultPrefix(string $defaultPrefix): self
    {
        $this->defaultPrefix = $defaultPrefix;

        return $this;
    }

    public function defaultAction(string $defaultAction): self
    {
        $this->defaultAction = $defaultAction;

        return $this;
    }

    public function getDefaultPrefix(): string
    {
        return (string) $this->defaultPrefix;
    }

    public function getDefaultNamespace(): string
    {
        return (string) $this->defaultNamespace;
    }

    public function getDefaultAction(): string
    {
        return (string) $this->defaultAction;
    }

    public function getRoutes(): array
    {
        return $this->routes;
    }

    public function getMiddlewares(): array
    {
        return $this->middlewares;
    }

    public function withRoutes(array $routes): self
    {
        $new = clone $this;
        static::validateArrayOfRoutes($routes);
        $new->routes = $routes;

        return $new;
    }

    public function withNamespace(string $defaultNamespace): self
    {
        $new = clone $this;
        $new->defaultNamespace = $defaultNamespace;

        return $new;
    }

    public function withPrefix(string $defaultPrefix): self
    {
        $new = clone $this;
        $new->defaultPrefix = $defaultPrefix;

        return $new;
    }

    protected function setOptions(Route $route): Route
    {
        $route = $this->setDefaultOptions($route);
        $this->getMiddlewares() and $route->middlewares(...$this->getMiddlewares());
        return $route;
    }

    protected function setDefaultOptions(Route $route): Route
    {
        !$route->getPrefix() and $this->getDefaultPrefix() and $route->prefix($this->getDefaultPrefix());
        !$route->getNamespace() and $route->namespace($this->getDefaultNamespace());
        !$route->getAction() and $route->action($this->getDefaultAction());

        return $route;
    }

    public function toCollections(): array
    {
        $collections = [];

        foreach ($this->routes as $route) {
            $route = $this->setOptions($route);
            $collections[] = $route->toCollections();
        }

        return array_merge(...$collections);
    }

    final public static function validateArrayOfRoutes(array $routes)
    {
        if (!Arr::all($routes, function ($route) {
            return $route instanceof Route;
        })) {
            throw new InvalidArgumentException(
                'Array of Routes only accepted.'
            );
        }
    }
}
