<?php
namespace App\Service\ServiceChecker;

use App\Service\Helper;
use App\Service\SchemeService\SchemeService;
use React\EventLoop\LoopInterface;
use React\EventLoop\Timer\Timer;
use Psr\Log\LoggerAwareTrait;
use Psr\Log\NullLogger;
use App\Service\SchemeStorage\Models\Contract as ContractModel;
use React\HttpClient\Client;
use React\HttpClient\Response;

/**
 * Class ServiceChecker
 * @package App\Service\ServiceChecker
 * @author Nikita Gerasimov <tariel-x@ya.ru>
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
     * @var Client
     */
    private $client;

    /**
     * timer to check schemes
     *
     * @var integer
     */
    private $timer = 0;

    const CHECK_URL = 'check_url';

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
        $this->client = new Client($loop);
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

    /**
     * Set timer to check schemes
     * @param  integer  $timer  timer to check schemes
     * @return  self
     */ 
    public function setTimer(int $timer)
    {
        $this->timer = $timer;
        return $this;
    }

    public function start()
    {
        $this->getLoop()->addPeriodicTimer($this->timer, function (Timer $timer) {
            $this->exec();
//            $timer->cancel();
        });
    }

    protected function exec()
    {
        /** @var ContractModel[] $contracts */
        $contracts = $this->getService()->getAll();
        $this->logger->debug('load contracts list');
        $this->logger->debug(sprintf('contracts count %d', count($contracts)));
        array_walk($contracts, [$this, 'updateContract']);
    }

    private function updateContract(ContractModel $contract)
    {
        $services = $contract->getServices();
        array_walk($services, function(array $service) use ($contract) {
            $this->loadContracts($contract, $service);
        });
    }

    private function loadContracts(ContractModel $contract, array $service)
    {
        $url = $service[self::CHECK_URL];
        $data = '';
        $request = $this->client->request('GET', $url);
        $request->on('response', function (Response $response) use (&$data, $url, $contract, $service) {
            $response->on('data', function ($chunk) use (&$data) {
                $data .= $chunk;
            });
            $response->on('end', function() use (&$data, $url, $contract, $service) {
                $this->logger->debug(sprintf('Loaded contracts from `%s`', $url));
                $this->checkContractService($contract, $service, $data);
            });
        });
        $request->on('error', function (\Exception $e) {
            echo $e;
        });
        $request->end();
    }

    private function checkContractService(ContractModel $contract, array $service, string $data)
    {
        $rawContracts = json_decode($data, true);
        foreach ($rawContracts as $rawContract) {
            $this->getService()->registerContract($rawContract['schemes'], $rawContract['service']);
            $this->logger->info(
                sprintf(
                    'Registering new or existing contract `%s` for service `%s`',
                    json_encode($rawContract['schemes']),
                    $rawContract['service']['name']
                )
            );
        }

        $schemesLoaded = array_map(function (array $schemes) {return $schemes['schemes'];}, $rawContracts);
        $keepContract = (new Helper())->arrayContainsArray($schemesLoaded, $contract->getSchemes());
        if ($keepContract === false) {
            $this->logger->info(
                sprintf(
                    'Due to service contracts info remove contract `%s` for service `%s`',
                    json_encode($contract->getSchemes()),
                    $service['name']
                )
            );
            $this->getService()->removeContract($contract->getSchemes(), $service);
        }
    }
}
