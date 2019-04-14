<?php 
namespace Baka\Router;

use Baka\Support\Arr;
use InvalidArgumentException;
use function array_merge;

class RouteGroup
{
    protected $defaultPrefix;
    protected $defaultNamespace;
    protected $routes = [];

    public function __construct(array $routes)
    {
        
        $this->routes = $routes;
    }

    public static function from(array $routes):self
    {
        return new self($routes);
    }
    
    public function add(Route $route):self
    {
        $this->routes[] = $route;
        
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

    public function getDefaultPrefix(): string 
    {
        return (string) $this->defaultPrefix;
    }

    public function getDefaultNamespace(): string 
    {
        return (string) $this->defaultNamespace;
    }

    public function getRoutes(): array
    {
        return $this->routes;
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

    public function getCollections(): array
    {
        $collections = [];
        foreach ($this->routes as $route) {
            !$route->getPrefix() and $route->prefix($this->getDefaultPrefix());
            !$route->getNamespace() and $route->namespace($this->getDefaultNamespace());

            $collections = array_merge($collections, $route->toCollections());
        }

       return $collections; 
    }

    final public static function validateArrayOfRoutes(array $routes)
    {
        if (!Arr::all($routes, function ($route) {
            return $route instanceof Route;
        })){
            throw new InvalidArgumentException(
                'Array of Routes only accepted.'
            );
        }
    }
}
