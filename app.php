<?php

use Psr\Http\Message\ServerRequestInterface;
use React\EventLoop\Factory;
use React\Http\Response;
use React\Http\Server;

require_once(__DIR__ . '/vendor/autoload.php');
require_once(__DIR__ . '/routes.php');
require_once(__DIR__ . '/handler.php');

$loop = React\EventLoop\Factory::create();

$server = new Server(function (ServerRequestInterface $request) {
    return handle($request);
});

$socket = new React\Socket\Server('0.0.0.0:8883', $loop);
$server->listen($socket);

$loop->run();
