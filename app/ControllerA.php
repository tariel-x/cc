<?php
namespace App;

use Psr\Http\Message\ServerRequestInterface;
use App\Model\Pyramid;
use App\Service\RPC\JsonRPC;
use React\Promise\Promise;
use React\Http\Response;

class ControllerA extends BaseController
{
    /**
     * json-rpc instance
     *
     * @var JsonRPC
     */
    private $jsonRPC;

    public function __construct(JsonRPC $jsonRPC)
    {
        $this->jsonRPC = $jsonRPC;
    }

    /**
     * base controller
     *
     * @param ServerRequestInterface $request
     * @param array $parameters
     * @return void
     */
    public function foo(ServerRequestInterface $request, array $parameters, string $body)
    {
        $result = $this->getTemplater()->render('home', [
            'date' => (new \DateTime())->format('c'),
        ]);
        return $this->returnHtml($result);
    }


    /**
     * base rpc handler
     *
     * @param ServerRequestInterface $request
     * @param array $parameters
     * @return void
     */
    public function rpcHandler(ServerRequestInterface $request, array $parameters, string $body) 
    {
        $call = json_decode($body, true);
        try {
            $result = $this->jsonRPC->handle($call);
        } catch (\Throwable $e) {
            printf("%s\n", $e->getMessage());
            return new Response(500, [], "");
        }
        
        return $this->returnJson($result);
    }
}
