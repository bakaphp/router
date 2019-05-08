<?php

declare(strict_types=1);

namespace Baka\Router\Middleware;

use Phalcon\Mvc\Micro;
use Phalcon\Utils\Slug;
use Baka\Router\Middleware;


class RouteMiddlewareHelper
{

    protected $api;
    protected $routeMiddlewares;

    public function __construct(Micro $api, array $routeMiddlewares)
    {
        $this->api = $api;
        $this->routeMiddlewares = $routeMiddlewares;
        
    }

    public function getRouteMiddlewares(string $event = null) : array
    {
        $routeIfentifier =  $this->getRouteIdentifier($this->api);

        $middlewares = $this->api->getSharedService('routeMiddlewares')[$routeIfentifier] ?? [];

        return array_filter($middlewares, function(Middleware $middleware) use ($event){
            
            $foundRouteMiddleware = $this->isInRouteMiddlewares(
                $middleware->getMiddlewareKey()
            );

            if($event){
               return $foundRouteMiddleware and $event === $middleware->getEvent();
            }

            return $foundRouteMiddleware;

        });

    }

    public function getRouteIdentifier(): string 
    {
        $activeHanlder = $this->api->getActiveHandler();

        return  Slug::generate(
            ($activeHanlder[0])->getDefinition().'-'.$activeHanlder[1]
        );
    }

    public function getClass(Middleware $middleware): string
    {
        $key = $middleware->getMiddlewareKey();

        return $this->routeMiddlewares[$key];

    }

    protected function isInRouteMiddlewares(string $key) : bool
    {
       return isset($this->routeMiddlewares[$key]);
    }

}
