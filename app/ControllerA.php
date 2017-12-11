<?php
namespace App;

use React\Http\Response;

class ControllerA extends BaseController
{
    public function foo(array $parameters)
    {
        $result = $this->getTemplater()->render('home', [
            'date' => $parameters['month'],
        ]);
        return $this->returnHtml($result);
    }

    public function newRecord(array $parameters)
    {
        $result = $this->getTemplater()->render('new_record');
        return $this->returnHtml($result);
    }
}
