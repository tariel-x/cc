<?php

use Psr\Http\Message\ServerRequestInterface;
use React\Http\Server;

require_once(__DIR__ . '/vendor/autoload.php');
require_once(__DIR__ . '/routes.php');
require_once(__DIR__ . '/handler.php');

//loop
$loop = React\EventLoop\Factory::create();

$logger = WyriHaximus\React\PSR3\Stdio\StdioLogger::create($loop)->withNewLine(true);

//redis
$redis = new Redis();
$redis->connect('127.0.0.1');
$logger->info('Connected redis');

//services
$schemeStorage = new \App\Service\SchemeStorage\RedisStorage($redis);
$schemeStorage->setLogger($logger);
$schemeService = new \App\Service\SchemeService\SchemeService($schemeStorage);
$schemeService->setLogger($logger);
$handlers = new \App\Service\RPC\Handlers($schemeService);
$jsonrpc = new \App\Service\RPC\JsonRPC($handlers);

//server
$server = new Server(function (ServerRequestInterface $request) {
    return handle($request);
});

//service checker
if (in_array('watch', $argv)) {
    $logger->info('Start watching contracts');
    $serviceChecker = new \App\Service\ServiceChecker\ServiceChecker($schemeService, $loop);
    $serviceChecker->setLogger($logger);
    $serviceChecker->start();
}

$socket = new React\Socket\Server('0.0.0.0:8883', $loop);
$server->listen($socket);
$logger->info('Server started at 0.0.0.0:8883');
$loop->run();
