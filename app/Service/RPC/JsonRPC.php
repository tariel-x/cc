<?php

namespace App\Service\RPC;

class JsonRPC
{
    private $handler;

    public function __construct()
    {
        $this->handler = new Handlers();
    }


    public function handle(array $req): array
    {
        $method = $req['method'];
        $params = $req['params'];
        $result = call_user_func_array([$this->handler, $method], $params);

        $return = $req;
        unset($return['method']);
        unset($return['params']);
        $return['result'] = $result;
        return $return;
    }
}