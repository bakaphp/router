<?php

require_once __DIR__ . '/vendor/autoload.php';

use Baka\Router\RouteGroup;
use Baka\Router\Route;
use Baka\Router\Http;

$routes = [
    Route::add('u')->controller('UsersController')->via([Http::GET, Http::POST]),
    Route::get('custom-fields'),
    Route::put('users')->action('test'),
    Route::add('companies'),
];

$anotherRoute = new Route('companies');

$anotherRoute->prefix('/v2')
->controller('CompaniesController')
->namespace('App\\Api\\Controllers')
->via("get|put|post");

$routeGroup = RouteGroup::from($routes)
->add(Route::put('products')->action('test'))
->add($anotherRoute)
->defaultPrefix('/Default')
->defaultNamespace('App\\Default\\Controllers')
->defaultAction('allDefault');

dump($routeGroup->getCollections()); 

// Add Phalcon Validation to Route before parse to Collection
/*
foreach($routeGroup->getCollections() as $collection){
  $application->mount($collection);
}
*/