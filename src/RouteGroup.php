<?php

namespace Baka\Router;

use Baka\Router\Utils\Helper;
use function array_push;

class RouteGroup
{
    protected $defaultPrefix;
    protected $defaultNamespace;
    protected $defaultAction;
    protected $routes = [];
    protected $middlewares = [];

    public function __construct(array $routes)
    {
        foreach ($routes as $route) {
            $this->addRoute($route);
        }
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
        array_push($this->middlewares,...$middlewares);

        return $this;
    }

    public function defaultNamespace(string $defaultNamespace): self
    {
        $this->defaultNamespace = Helper::trimSlahes($defaultNamespace);

        return $this;
    }

    public function defaultPrefix(string $defaultPrefix): self
    {
        $this->defaultPrefix = Helper::trimSlahes($defaultPrefix);

        return $this;
    }

    public function defaultAction(string $defaultAction): self
    {
        $this->defaultAction = Helper::trimSlahes($defaultAction);

        return $this;
    }

    public function getDefaultPrefix(): ?string
    {
        return $this->defaultPrefix;
    }

    public function getDefaultNamespace(): ?string
    {
        return $this->defaultNamespace;
    }

    public function getDefaultAction(): ?string
    {
        return $this->defaultAction;
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

        foreach ($routes as $route) {
            $new->addRoute($route);
        }

        return $new;
    }

    public function withNamespace(string $defaultNamespace): self
    {
        $new = clone $this;
        $new->defaultNamespace($defaultNamespace);

        return $new;
    }

    public function withPrefix(string $defaultPrefix): self
    {
        $new = clone $this;
        $new->defaultPrefix($defaultPrefix);

        return $new;
    }

    public function withAction(string $defaultAction): self
    {
        $new = clone $this;
        $new->defaultAction($defaultAction);

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
        !$route->getNamespace() and $this->getDefaultNamespace() and $route->namespace($this->getDefaultNamespace());
        !$route->getAction() and $this->getDefaultAction() and $route->action($this->getDefaultAction());

        return $route;
    }

    public function toCollections(): array
    {
        $collections = [];

        foreach ($this->routes as $route) {
            $route = $this->setOptions($route);
            array_push($collections,...$route->toCollections());
        }

        return $collections;
    }
}
