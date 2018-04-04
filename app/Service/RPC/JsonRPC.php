<?php

namespace App\Service\RPC;

class JsonRPC
{
    private $handler;

    public function __construct(Handlers $handler)
    {
        $this->handler = $handler;
    }


    public function handle(array $req): array
    {
        $method = $req['method'];
        $params = $req['params'];
        $result = $this->call_user_function_named_param([$this->handler, $method], $params);

        $return = $req;
        unset($return['method']);
        unset($return['params']);
        $return['result'] = $result;
        return $return;
    }
    //TODO: REFACTOR!!!
    private function call_user_function_named_param($call_arg, array $param_array)
    {
        $Func = null;
        $Method = null;
        $Object = null;
        $Class = null;
        // The cases. f means function name
        // Case1: f({object, method}, params)
        // Case2: f({class, function}, params)
        if(is_array($call_arg) && count($call_arg) == 2)
        {
            if(is_object($call_arg[0]))
            {
                $Object = $call_arg[0];
                $Class = get_class($Object);
            }
            else if(is_string($call_arg[0]))
            {
                $Class = $call_arg[0]; 
            }
            if(is_string($call_arg[1]))
            {
                $Method = $call_arg[1];
            }
        }
        // Case3: f("class::function", params)
        else if(is_string($call_arg) && strpos($call_arg, "::") !== FALSE)
        {
            list($Class, $Method) = explode("::", $call_arg);
        }
        // Case4: f("function", params)
        else if(is_string($call_arg) && strpos($call_arg, "::") === FALSE)
        {
            $Method = $call_arg;
        }
        // Case5: f(closure, params)
        else if(is_object($call_arg) && $call_arg instanceof \Closure)
        {
            $Method = $call_arg;
        }
        else throw new \Exception("Case not allowed! Invalid Data supplied!");
        if($Class) $Func = new \ReflectionMethod($Class, $Method);
        else $Func = new \ReflectionFunction($Method);
        $params = array();
        foreach($Func->getParameters() as $Param)
        {
            if($Param->isDefaultValueAvailable()) $params[$Param->getPosition()] = $Param->getDefaultValue();
            if(array_key_exists($Param->name, $param_array)) $params[$Param->getPosition()] = $param_array[$Param->name];
            if(!$Param->isOptional() && !isset($params[$Param->getPosition()])) die("No Defaultvalue available and no Value supplied!\r\n");
        }
        if($Func instanceof \ReflectionFunction) return $Func->invokeArgs($params);
        if($Func->isStatic()) return $Func->invokeArgs(null, $params);
        else return $Func->invokeArgs($Object, $params);
    }
}