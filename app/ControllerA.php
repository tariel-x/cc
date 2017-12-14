<?php
namespace App;

use Psr\Http\Message\ServerRequestInterface;
use App\Model\Pyramid;
use App\Service\Gateway;

class ControllerA extends BaseController
{
    public function foo(ServerRequestInterface $request, array $parameters)
    {
        $result = $this->getTemplater()->render('home', [
            'date' => $parameters['month'],
        ]);
        return $this->returnHtml($result);
    }

    public function newRecord(ServerRequestInterface $request, array $parameters)
    {
        $model = new Pyramid();
        $mapper = new Mapper();

        $mapped = $mapper->map($request->getQueryParams(), $model);
        if ($mapped) {
            $service = new Gateway('http:/127.0.0.1:4443');
            if ($service->addPyramid($model)) {
                $model = new Pyramid();
            } else {
                $result = $this->getTemplater()->render('new_record', [
                    'model' => $model,
                    'error' => 'not saved',
                ]);
                return $this->returnHtml($result);
            }
        }


        $result = $this->getTemplater()->render('new_record', [
            'model' => $model,
        ]);
        return $this->returnHtml($result);
    }
}
