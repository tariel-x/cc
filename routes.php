<?php

use Symfony\Component\Routing\RouteCollection;
use Symfony\Component\Routing\Route;
use App\ControllerA;

$routes = new RouteCollection();

$route = new Route('/foo', ['_controller' => ControllerA::class . "::foo"]);
$routes->add('foo', $route);

$route = new Route('/new_record', ['_controller' => ControllerA::class . "::newRecord"]);
$routes->add('newRecord', $route);

$route = new Route(
    '/archive/{month}', // path
    array('_controller' => ControllerA::class . "::foo"), // default values
    array('month' => '[0-9]{4}-[0-9]{2}', 'subdomain' => 'www|m') // requirements
);
$routes->add('archive', $route);