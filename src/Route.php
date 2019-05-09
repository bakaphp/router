<?php

namespace Baka\Router;

use Baka\Support\Str;
use Baka\Router\Utils\Http;
use Baka\Router\Parser\RouteParser;
use function array_intersect;

class Route
{

    const DEFAULT_HTTP_METHODS = [
        Http::POST,
        Http::GET,
        Http::PUT,
        Http::PATCH,
        Http::DELETE,
    ];

    protected $path;
    protected $action;
    protected $prefix;
    protected $namespace;
    protected $controller;
    protected $via = [];
    protected $middlewares = [];

    public function __construct(string $path)
    {
        $this->path($path);
    }

    /**
     * Create a Route instance based on the path given accessible through all default methods
     *
     *@get (/path)
     *@get (/path/{id:[0-9]+)
     *@post (/path)
     *@put (/path/{id:[0-9]+)
     *@path (/path/{id:[0-9]+)
     *@delete (/path/{id:[0-9]+)
     *
     * @param string $path
     *
     * @return self
     */
    public static function add(string $path): self
    {
        $route = new self($path);
        $route->via(...static::DEFAULT_HTTP_METHODS);

        return $route;
    }

    /**
     * Create a Route instance based on the path given accessible only through get method
     *
     *@get (/path)
     *@get (/path/{id:[0-9]+)
     *
     * @param string $path
     *
     * @return self
     */
    public static function get(string $path): self
    {
        $route = new self($path);
        $route->via(Http::GET);

        return $route;
    }


    /**
     * Create a Route instance based on the path given accessible only through post method
     *
     *@post (/path)
     *
     * @param string $path
     *
     * @return self
     */
    public static function post(string $path): self
    {
        $route = new self($path);
        $route->via(Http::POST);

        return $route;
    }

    /**
     * CCreate a Route instance based on the path given accessible only through put method
     *
     *@put (/path/{id:[0-9]+)
     *
     * @param string $path
     *
     * @return self
     */
    public static function put(string $path): self
    {
        $route = new self($path);
        $route->via(Http::PUT);

        return $route;
    }

    /**
     * Create a Route instance based on the path given accessible only through patch method
     *
     *@patch (/path/{id:[0-9]+)
     *
     * @param string $path
     *
     * @return self
     */
    public static function patch(string $path): self
    {
        $route = new self($path);
        $route->via(Http::PATCH);

        return $route;
    }

    /**
     * Create a Route instance based on the path given accessible only through delete method
     *
     *@delete (/path/{id:[0-9]+)
     *
     * @param string $path
     *
     * @return self
     */
    public static function delete(string $path): self
    {
        $route = new self($path);
        $route->via(Http::DELETE);

        return $route;
    }

    /**
     * Return an array of Collection instances based on the Route
     *
     * @return array
     */
    public function toCollections(): array
    {
        $this->setDefaultOptions();
        $parser = new RouteParser($this);

        return $parser->parse();
    }

    /**
     * Set a prefix to the route
     *
     * @param string $prefix
     * @return self
     */
    public function prefix(string $prefix): self
    {
        if (!Str::startsWith($prefix, '/')) {
            $prefix = '/' . $prefix;
        }

        $this->prefix = $prefix;

        return $this;
    }
    
    /**
     * Set a namespace to the route
     *
     * @param string $prefix
     * @return self
     */
    public function namespace(string $namespace): self
    {
        $this->namespace = $namespace;

        return $this;
    }

    /**
     * Set a controller to the route
     *
     * @param string $prefix
     * @return self
     */
    public function controller(string $controller): self
    {
        $this->controller = $controller;

        return $this;
    }

    /**
     * Set the methods which this route will be accessible.
     * This method filters the given methods in order to only add the valid ones.
     *
     * @param string $prefix
     * @return self
     */
    public function via(...$methods): self
    {

        $this->via = array_intersect(
                $methods,
                Http::METHODS
            );

        return $this;
    }

    /**
     * Set the path to match the route
     *
     * @param string $path
     * @return self
     */
    public function path(string $path): self
    {
        if (!Str::startsWith($path, '/')) {
            $path = '/' . $path;
        }

        $this->path = $path;

        return $this;
    }

    /**
     * Set the method that will be call when the route is matched
     *
     * @param string $action
     * @return self
     */
    public function action(string $action): self
    {
        $this->action = !$action ? null : $action;

        return $this;
    }

    /**
     * Set middlewares to the current route
     *
     * @param [mixed] ...$middlewares
     * @return self
     */
    public function middlewares(...$middlewares): self
    {
        $this->middlewares = array_merge($this->middlewares, $middlewares);

        return $this;
    }

    /**
     * Get the route prefix
     *
     * @return string
     */
    public function getPrefix(): string
    {
        return (string) $this->prefix;
    }

    /**
     * Return the route namespace
     *
     * @return string
     */
    public function getNamespace(): string
    {
        return (string) $this->namespace;
    }

    /**
     * Return the route controller
     *
     * @return string
     */
    public function getController(): string
    {
        return (string) $this->controller;
    }

    /**
     * Return the route http methods
     *
     * @return array
     */
    public function getVia(): array
    {
        return $this->via;
    }

    /**
     * Return the route middlewares
     *
     * @return array
     */
    public function getMiddlewares(): array
    {
        return $this->middlewares;
    }

    /**
     * Return the route path
     *
     * @return string
     */
    public function getPath(): string
    {
        return $this->path;
    }

    /**
     * Return the route action
     *
     * @return string|null
     */
    public function getAction(): ?string
    {
        return $this->action;
    }

    /**
     * Return the collection pattern
     *
     * @return string
     */
    public function getPattern(): string
    {
        return $this->getPrefix() . $this->getPath();
    }

    /**
     * Return the collection hablnder
     *
     * @return string
     */
    public function getHanlder(): string
    {
        return $this->getNamespace() . '\\' . $this->getController();
    }

    /**
     * Return a copy of the Route with the given prefix set
     *
     * @param string $prefix
     * @return self
     */
    public function withPrefix(string $prefix): self
    {
        $new = clone $this;
        $new->prefix = $prefix;

        return $new;
    }

    /**
     * Return a copy of the Route with the given namespace set
     *
     * @param string $namespace
     * @return self
     */
    public function withNamespace(string $namespace): self
    {
        $new = clone $this;
        $new->namespace = $namespace;

        return $new;
    }

    /**
     * Return a copy of the Route with the given controller set
     *
     * @param string $controller
     * @return self
     */
    public function withController(string $controller): self
    {
        $new = clone $this;
        $new->controller = $controller;

        return $new;
    }

    /**
     * Return a copy of the Route with the given http methods set
     *
     * @param $methods
     * @return self
     */
    public function withVia($methods): self
    {
        $new = clone $this;
        $new->methods = $methods;

        return $new;
    }

    /**
     * Return a copy of the Route with the given path set
     *
     * @param string $path
     * @return self
     */
    public function withPath(string $path): self
    {
        $new = clone $this;
        $new->path = $path;

        return $new;
    }

    /**
     * Return a copy of the Route with the given action set
     *
     * @param string $action
     * @return self
     */
    public function withAction(string $action): self
    {
        $new = clone $this;
        $new->action = $action;

        return $new;
    }

    /**
     * Set all the empty properties with default options
     *
     * @return void
     */
    protected function setDefaultOptions(): void
    {
        !$this->getVia() and $this->setDefaultVia();
        !$this->getController() and $this->setDefaultController();
    }

    /**
     * Set default http methods as via
     *
     * @return void
     */
    protected function setDefaultVia(): void
    {
        $this->via(...static::DEFAULT_HTTP_METHODS);
    }

    /**
     * Set the controller property based on the path given
     *
     * @return void
     */
    protected function setDefaultController(): void
    {
        $path = preg_replace('/[^a-zA-Z]/', '', $this->getPath());

        $this->controller(
        Str::camelize($path, '-') . 'Controller'
       );
    }
}
