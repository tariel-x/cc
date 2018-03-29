<?php
namespace App\Service\ServiceChecker;

use React\EventLoop\LoopInterface;
use React\EventLoop\Timer\Timer;
use Psr\Log\LoggerAwareTrait;
use Psr\Log\NullLogger;
use App\Service\SchemeService\SchemeService;
use App\Service\SchemeStorage\Models\Contract as ContractModel;

/**
 * Class ServiceChecker
 * @package App\Service\ServiceChecker
 * @author Nikita Gerasimov <nikita.gerasimov@corp.mail.ru>
 */
class ServiceChecker
{
    use LoggerAwareTrait;

    /**
     * @var SchemeService
     */
    private $service;

    /**
     * @var LoopInterface
     */
    private $loop;

    /**
     * ServiceChecker constructor.
     * @param SchemeService $service
     * @param LoopInterface $loop
     */
    public function __construct(SchemeService $service, LoopInterface $loop)
    {
        $this->service = $service;
        $this->loop = $loop;
        $this->logger = new NullLogger();
    }

    /**
     * @return SchemeService
     */
    public function getService(): SchemeService
    {
        return $this->service;
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
            $this->exec();
        });
    }

    protected function exec()
    {
        /** @var ContractModel[] $contracts */
        $contracts = $this->getService()->getAll();
        $this->logger->debug('load contracts list');
        $this->logger->debug(count($contracts));
    }
}
