<?php

use Symfony\Component\Routing\RouteCollection;
use Symfony\Component\Routing\Route;
use App\ControllerA;

$routes = new RouteCollection();

$route = new Route('/', ['_controller' => ControllerA::class . "::foo"]);
$routes->add('foo', $route);

$route = new Route('/rpc', ['_controller' => ControllerA::class . "::rpcHandler"]);
$routes->add('rpc', $route);
