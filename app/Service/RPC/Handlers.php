<?php

namespace App\Service\RPC;

class Handlers
{
    /**
     * status function
     *
     * @return array
     */
    public function status(): array
    {
        return [
            'status' => 'ok',
        ];
    }
}