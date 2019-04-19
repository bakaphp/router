<?php

namespace Baka\Router;

use InvalidArgumentException;


class Middleware 
{
    const BEFORE = 'before';
    const AFTER = 'after';

    const EVENTS = [self::AFTER, self::BEFORE];

    protected $middlewareKey;
    protected $parameters = [];
    protected $event = self::BEFORE;

    public function __construct(string $middlewareKey)
    {
        $this->middlewareKey  = $middlewareKey;
    }

    public function event(string $event): void 
    {
        if(!in_array($event, static::EVENTS)){
            throw new InvalidArgumentException("Only before and after are accepted events.");
        }
        $this->event = $event;
    }

    public function parameters(array $parameters): void
    {
        $this->parameters = $parameters;
    }

    public function getMiddlewareKey(): string
    {
        return $this->middlewareKey;
    }
    
    public function getParameters(): array
    {
        return $this->parameters;
    }

    public function getEvent(): string
    {
        return $this->event;
    }

}