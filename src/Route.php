<?php

namespace Baka\Router;

use Baka\Support\Str;
use InvalidArgumentException;
use function is_array;
use function explode;
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

    public function __construct(string $path)
    {
        $this->path($path);
    }

    public static function add(string $path) : self
    {
        $route = new self($path);
        $route->via(static::DEFAULT_HTTP_METHODS);

        return $route;
    }

    public static function get(string $path) : self
    {
        $route = new self($path);
        $route->via([Http::GET]);
        
        return $route;

    }
    public static function post(string $path) : self
    {
        $route = new self($path);
        $route->via([Http::POST]);
        
        return $route;

    }
    public static function put(string $path) : self
    {
        $route = new self($path);
        $route->via([Http::PUT]);
        
        return $route;

    }
    public static function patch(string $path) : self
    {
        $route = new self($path);
        $route->via([Http::PATCH]);
        
        return $route;

    }

    public static function delete(string $path) : self
    {
        $route = new self($path);
        $route->via([Http::DELET]);
        
        return $route;
    }

    public static function options(string $path) : self
    {
        $route = new self($path);
        $route->via([Http::OPTIONS]);
        
        return $route;
    }

    public static function head(string $path) : self
    {
        $route = new self($path);
        $route->via([Http::HEAD]);
        
        return $route;
    }

    public function toCollection() : array
    {
        $this->populateEmptyProperties();
        $parser = new RouteParser($this);

        return $parser->parse();
    }

    public function prefix(string $prefix): self 
    {
        if(!Str::startsWith($prefix,'/')){
            $prefix = '/'.$prefix;
        }

        $this->prefix = $prefix;

        return $this;
    }

    public function namespace(string $namespace): self 
    {
        $this->namespace = $namespace;

        return $this;
    }

    public function controller(string $controller): self 
    {
        $this->controller = $controller;

        return $this;
    }

    public function via($methods): self 
    {
        if(!is_array($methods) and !is_string($methods)){
            throw new InvalidArgumentException(
                'Array or string are only accepted.'
            );
        }

        if(!is_array($methods)){
            $methods = explode('|', $methods);
        }

          $this->via = array_intersect(
                $methods,
                Http::METHODS
            );

        return $this;
    }
    
    public function path(string $path): self
    {
        if(!Str::startsWith($path,'/')){
            $path = '/'.$path;
        }

        $this->path = $path;

        return $this;
    }

    public function action(string $action): self
    {
        $this->action = $action;

        return $this;
    }

    public function getPrefix(): string 
    {
        return (string) $this->prefix;
    }

    public function getNamespace(): string 
    {
        return (string) $this->namespace;
    }

    public function getController(): string 
    {
        return (string) $this->controller;
    }

    public function getVia(): array 
    {
        return $this->via;
    }
    
    public function getPath(): string
    {
        return $this->path;
    }

    public function getAction(): ?string
    {
        return $this->action;
    }

    public function getPattern(): string
    {
        return $this->getPrefix().$this->getPath();
    }

    public function getHanlder(): string
    {
        return $this->getNamespace()."\\".$this->getController();
    }

    public function withPrefix(string $prefix): self
    {
        $new = clone $this;
        $new->prefix = $prefix;

        return $new;
    }

    public function withNamespace(string $namespace): self
    {
        $new = clone $this;
        $new->namespace = $namespace;

        return $new;
    }

    public function withController(string $controller): self
    {
        $new = clone $this;
        $new->controller = $controller;

        return $new;
    }

    public function withVia($methods): self 
    {
        $new = clone $this;
        $new->methods = $methods;

        return $new;
    }
    
    public function withPath(string $path): self
    {
        $new = clone $this;
        $new->path = $path;

        return $new;
    }

    public function withAction(string $action): self
    {
        $new = clone $this;
        $new->action = $action;

        return $new;
    }

    protected function populateEmptyProperties(): void 
    {
        !$this->getVia() and $this->setDefaultVia();
        !$this->getController() and $this->setDefaultController();
    }

    protected function setDefaultVia(): void
    {
        $this->via(static::DEFAULT_HTTP_METHODS);
    }
    
    protected function setDefaultController(): void
    {
        $path = preg_replace('/[^a-zA-Z]/', '', $this->getPath());
        
        $this->controller(
        Str::camelize($path, '-').'Controller'
       );
    }
}
