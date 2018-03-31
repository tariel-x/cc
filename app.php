<?php

use Psr\Http\Message\ServerRequestInterface;
use React\Http\Server;

require_once(__DIR__ . '/vendor/autoload.php');
require_once(__DIR__ . '/routes.php');
require_once(__DIR__ . '/handler.php');

$config = \App\Yaml::parse(file_get_contents(__DIR__.'/config.yml'));

//loop
$loop = React\EventLoop\Factory::create();

$logger = WyriHaximus\React\PSR3\Stdio\StdioLogger::create($loop)->withNewLine(true);

//redis
$redis = new Redis();
$redis->connect($config['redis']['host']);

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
    $serviceChecker->setTimer((int)$config['watch']['timer']);
    $serviceChecker->start();
}

$socket = new React\Socket\Server($config['server']['host'], $loop);
$server->listen($socket);
$logger->info(sprintf('Server started at %s', $config['server']['host']));
$loop->run();
