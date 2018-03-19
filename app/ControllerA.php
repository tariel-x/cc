<?php
namespace App;

use Psr\Http\Message\ServerRequestInterface;
use App\Model\Pyramid;
use App\Service\RPC\JsonRPC;

class ControllerA extends BaseController
{
    /**
     * json-rpc instance
     *
     * @var JsonRPC
     */
    private $jsonRPC;

    public function __construct()
    {
        $this->jsonRPC = new JsonRPC();
    }

    /**
     * base controller
     *
     * @param ServerRequestInterface $request
     * @param array $parameters
     * @return void
     */
    public function foo(ServerRequestInterface $request, array $parameters)
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
    public function rpcHandler(ServerRequestInterface $request, array $parameters) 
    {
        print "aa\n";        
        $body = $request->getBody()->getContents();
        var_dump($body);
        $call = json_decode($body, true);
        var_dump($call);
        $result = $this->jsonRPC->handle($call);
        return $this->returnJson($result);
    }
}
