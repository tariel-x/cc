<?php
namespace App\Service\ServiceChecker;

use App\Service\Helper;
use App\Service\SchemeService\SchemeServiceInterface;
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
     * @var SchemeServiceInterface
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
     * @param SchemeServiceInterface $service
     * @param LoopInterface $loop
     */
    public function __construct(SchemeServiceInterface $service, LoopInterface $loop)
    {
        $this->service = $service;
        $this->loop = $loop;
        $this->logger = new NullLogger();
        $this->client = new Client($loop);
    }

    /**
     * @return SchemeServiceInterface
     */
    public function getService(): SchemeServiceInterface
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
        array_walk($contracts, [$this, 'updateUsage']);
    }

    /**
     * Update found local contract - services
     *
     * @param ContractModel $contract
     * @return void
     */
    private function updateContract(ContractModel $contract)
    {
        $services = $contract->getServices();
        array_walk($services, function(array $service) use ($contract) {
            $this->loadContracts($contract->getSchemes(), $service, false);
        });
    }

    /**
     * Update found local contract - usages
     *
     * @param ContractModel $contract
     * @return void
     */
    private function updateUsage(ContractModel $contract)
    {
        $usages = $contract->getUsages();
        array_walk($usages, function(array $usage) use ($contract) {
            $this->loadContracts($contract->getSchemes(), $usage, true);
        });
    }

    /**
     * Load current contract schemes
     *
     * @param array $currentSchemes
     * @param array $service
     * @return void
     */
    private function loadContracts(array $currentSchemes, array $service, bool $usage)
    {
        $url = $service[self::CHECK_URL];
        $data = '';
        $request = $this->client->request('GET', $url);
        $request->on('response', function (Response $response) use (&$data, $url, $currentSchemes, $service, $usage) {
            $response->on('data', function ($chunk) use (&$data) {
                $data .= $chunk;
            });
            $response->on('end', function() use (&$data, $url, $currentSchemes, $service, $usage) {
                $this->logger->debug(sprintf('Loaded contracts from `%s`', $url));
                $this->checkContractService($currentSchemes, $service, $data, $usage);
            });
        });
        $request->on('error', function (\Exception $e) {
            echo $e;
        });
        $request->end();
    }

    /**
     * This is one main function to add or remove contracts and usages
     * TODO: refactor
     *
     * @param array $currentSchemes
     * @param array $service
     * @param string $data
     * @param boolean $usage
     * @return void
     */
    private function checkContractService(array $currentSchemes, array $service, string $data, bool $usage)
    {
        $serviceKey = $usage ? 'usage' : 'service';
        $rawContracts = json_decode($data, true);

        if (!is_array($rawContracts)) {
            $this->remove($currentSchemes, $service, $usage);
            return;
        }

        foreach ($rawContracts as $rawContract) {

            if (!array_key_exists('schemes', $rawContract)) {
                $this->remove($currentSchemes, $service, $usage);
                return;
            }

            if (!array_key_exists($serviceKey, $rawContract)) {
                return;
            }

            if (!$usage) {
                $this->getService()->registerContract($rawContract['schemes'], $rawContract[$serviceKey]);
            } else {
                $this->getService()->registerUsage($rawContract['schemes'], $rawContract[$serviceKey]);
            }
            $this->logger->info(
                sprintf(
                    'Registering new or existing contract `%s` for service `%s`',
                    json_encode($rawContract['schemes']),
                    $rawContract[$serviceKey]['name']
                )
            );
        }

        $schemesLoaded = array_map(function (array $schemes) {return $schemes['schemes'];}, $rawContracts);
        $keepContract = (new Helper())->arrayContainsArray($schemesLoaded, $currentSchemes);
        if ($keepContract === false) {
            $this->logger->info(
                sprintf(
                    'Due to service contracts info remove contract `%s` for service `%s`',
                    json_encode($currentSchemes),
                    $service['name']
                )
            );
            $this->remove($currentSchemes, $service, $usage);
        }
    }

    private function remove(array $currentSchemes, array $service, bool $usage)
    {
        if (!$usage) {
            $this->getService()->removeContract($currentSchemes, $service);
        } else {
            $this->getService()->removeUsage($currentSchemes, $service);
        }
    }
}
