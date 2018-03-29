<?php
namespace App\Service\ServiceChecker;

use React\EventLoop\LoopInterface;
use React\EventLoop\Timer\Timer;
use WyriHaximus\React\ChildProcess\Closure\ClosureChild;
use WyriHaximus\React\ChildProcess\Closure\MessageFactory;
use WyriHaximus\React\ChildProcess\Messenger\Factory as MessengerFactory;
use WyriHaximus\React\ChildProcess\Messenger\Messages\Payload;
use WyriHaximus\React\ChildProcess\Messenger\Messenger;
use Psr\Log\LoggerAwareTrait;
use Psr\Log\NullLogger;

use App\Service\SchemeStorage\SchemeStorageInterface;

/**
 * Class ServiceChecker
 * @package App\Service\ServiceChecker
 * @author Nikita Gerasimov <nikita.gerasimov@corp.mail.ru>
 */
class ServiceChecker
{
    use LoggerAwareTrait;

    /**
     * @var SchemeStorageInterface
     */
    private $storage;

    /**
     * @var LoopInterface
     */
    private $loop;

    /**
     * ServiceChecker constructor.
     * @param SchemeStorageInterface $storage
     * @param LoopInterface $loop
     */
    public function __construct(SchemeStorageInterface $storage, LoopInterface $loop)
    {
        $this->storage = $storage;
        $this->loop = $loop;
        $this->logger = new NullLogger();
    }

    /**
     * @return SchemeStorageInterface
     */
    public function getStorage(): SchemeStorageInterface
    {
        return $this->storage;
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
        $this->getLoop()->addPeriodicTimer(2, function (Timer $timer) {
            $this->logger->debug('tick');
            $this->exec();
        });
    }

    protected function exec(): array
    {
        $this->logger->debug('exec');
        return ['time' => time()];
    }
}
