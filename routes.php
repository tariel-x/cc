<?php

use Symfony\Component\Routing\RouteCollection;
use Symfony\Component\Routing\Route;
use App\ControllerA;

$routes = new RouteCollection();

$route = new Route('/', ['_controller' => ControllerA::class . "::foo"]);
$routes->add('foo', $route);

$route = new Route('/new_record', ['_controller' => ControllerA::class . "::newRecord"]);
$routes->add('newRecord', $route);

$route = new Route('/stat', ['_controller' => ControllerA::class . "::getStat"]);
$routes->add('select_stat', $route);