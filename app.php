<?php

use Psr\Http\Message\ServerRequestInterface;
use React\Http\Server;

require_once(__DIR__ . '/vendor/autoload.php');
require_once(__DIR__ . '/routes.php');
require_once(__DIR__ . '/handler.php');

//redis
$redis = new Redis();
$redis->connect('127.0.0.1');

//services
$schemeStorage = new \App\Service\SchemeStorage\RedisStorage($redis);
$schemeService = new \App\Service\SchemeService\SchemeService($schemeStorage);
$handlers = new \App\Service\RPC\Handlers($schemeService);
$jsonrpc = new \App\Service\RPC\JsonRPC($handlers);

//server
$loop = React\EventLoop\Factory::create();

$server = new Server(function (ServerRequestInterface $request) {
    return handle($request);
});

$socket = new React\Socket\Server('0.0.0.0:8883', $loop);
$server->listen($socket);
print "Serer started at 0.0.0.0:8883\n";
$loop->run();
