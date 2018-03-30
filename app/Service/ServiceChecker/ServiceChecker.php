<?php
namespace App\Service\ServiceChecker;

use React\EventLoop\LoopInterface;
use React\EventLoop\Timer\Timer;
use Psr\Log\LoggerAwareTrait;
use Psr\Log\NullLogger;
use App\Service\SchemeStorage\SchemeStorageInterface;
use App\Service\SchemeStorage\Models\Contract as ContractModel;

/**
 * Class ServiceChecker
 * @package App\Service\ServiceChecker
 * @author Nikita Gerasimov <tariel-x@ya.ru>
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
            $this->exec();
        });
    }

    protected function exec()
    {
        /** @var ContractModel[] $contracts */
        $contracts = $this->getStorage()->getAllContracts();
        $this->logger->debug('load contracts list');
        $this->logger->debug(sprintf('contracts count %d', count($contracts)));
        array_walk($contracts, [$this, 'updateContract']);
    }

    private function updateContract(ContractModel $contract)
    {
        array_walk($contract->getServices(), function(array $service) use ($contract) {
            $this->checkServiceProvide($contract, $service);
        });
    }

    private function checkServiceProvide(ContractModel $contract, array $service)
    {
        $url = $service['contracts_url'];
        //load list of contracts
        $contracts = [];
    }
}
