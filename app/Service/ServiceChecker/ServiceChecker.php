<?php
namespace App\Service\ServiceChecker;

use React\EventLoop\LoopInterface;

use React\EventLoop\Timer\Timer;
use WyriHaximus\React\ChildProcess\Closure\ClosureChild;
use WyriHaximus\React\ChildProcess\Closure\MessageFactory;
use WyriHaximus\React\ChildProcess\Messenger\Factory as MessengerFactory;
use WyriHaximus\React\ChildProcess\Messenger\Messages\Payload;
use WyriHaximus\React\ChildProcess\Messenger\Messenger;

/**
 * Class ServiceChecker
 * @package App\Service\ServiceChecker
 * @author Nikita Gerasimov <nikita.gerasimov@corp.mail.ru>
 */
class ServiceChecker
{
    /**
     * @var \Redis
     */
    private $redis;

    /**
     * @var LoopInterface
     */
    private $loop;

    /**
     * ServiceChecker constructor.
     * @param \Redis $redis
     * @param LoopInterface $loop
     */
    public function __construct(\Redis $redis, LoopInterface $loop)
    {
        $this->redis = $redis;
        $this->loop = $loop;
    }

    /**
     * @return \Redis
     */
    public function getRedis(): \Redis
    {
        return $this->redis;
    }

    /**
     * @return LoopInterface
     */
    public function getLoop(): LoopInterface
    {
        return $this->loop;
    }

    public function start()
    {
        MessengerFactory::parentFromClass(ClosureChild::class, $this->getLoop())->then(function (Messenger $messenger) {
            $messenger->on('error', function ($e) {
                echo 'Error: ', var_export($e, true), PHP_EOL;
            });
            $i = 0;
            $this->getLoop()->addPeriodicTimer(5, function (Timer $timer) use (&$i, $messenger) {
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
    }
}
