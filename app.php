<?php

use Psr\Http\Message\ServerRequestInterface;
use React\Http\Server;

use React\EventLoop\Factory as EventLoopFactory;
use React\EventLoop\Timer\Timer;
use WyriHaximus\React\ChildProcess\Closure\ClosureChild;
use WyriHaximus\React\ChildProcess\Closure\MessageFactory;
use WyriHaximus\React\ChildProcess\Messenger\Factory as MessengerFactory;
use WyriHaximus\React\ChildProcess\Messenger\Messages\Payload;
use WyriHaximus\React\ChildProcess\Messenger\Messenger;


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

//////////////////

MessengerFactory::parentFromClass(ClosureChild::class, $loop)->then(function (Messenger $messenger) use ($loop) {
    $messenger->on('error', function ($e) {
        echo 'Error: ', var_export($e, true), PHP_EOL;
    });
    $i = 0;
    $loop->addPeriodicTimer(5, function (Timer $timer) use (&$i, $messenger) {
        if ($i >= 13) {
            $timer->cancel();
            $messenger->softTerminate();
            return;
        }
        $messenger->rpc(MessageFactory::rpc(function () {
            return ['time' => time()]; // Note that you ALWAYS MUST return an array
        }))->done(function (Payload $payload) {
            echo $payload['time'], PHP_EOL;
        });
        $i++;
    });
});

/////////////////

$socket = new React\Socket\Server('0.0.0.0:8883', $loop);
$server->listen($socket);
$logger->info('Server started at 0.0.0.0:8883');
$loop->run();
