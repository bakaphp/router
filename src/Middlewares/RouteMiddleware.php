<?php

declare(strict_types=1);

namespace Baka\Router\Middlewares;

use Phalcon\Mvc\Micro;
use Phalcon\Mvc\Micro\MiddlewareInterface;
use Baka\Router\Middleware;

class RouteMiddleware implements MiddlewareInterface
{
    protected $helper;

    public function __construct(Micro $api, array $routeMiddlewares)
    {
        $this->helper = new RouteMiddlewareHelper($api, $routeMiddlewares);
    }

    public function beforeExecuteRoute($event, $api, $contex)
    {
        foreach ($this->helper->getRouteMiddlewares(Middleware::BEFORE) as $middleware) {
            if(!$this->executeMiddleware($middleware, $api)){
                return false;
            };
        }

       return true;

    }

    public function afterExecuteRoute($event, $api, $contex)
    {
        foreach ($this->helper->getRouteMiddlewares(Middleware::AFTER) as $middleware) {
            if(!$this->executeMiddleware($middleware, $api)){
                return false;
            };
        }

        return true;
    }

    /**
     * Call me
     *
     * @param Micro $api
     *
     * @return bool
     */
    public function call(Micro $api)
    {
        return true;
    }


    protected function executeMiddleware(Middleware $middleware, Micro $api)
    {
        $middlewareClass = $this->helper->getClass(
            $middleware
        );

        $middlewareInstance = new $middlewareClass();

         return $middlewareInstance->call(
            $api,
            ...$middleware->getParameters()
        );
    }

}
