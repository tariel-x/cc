<?php
namespace App;

use Psr\Http\Message\ServerRequestInterface;
use App\Model\Pyramid;
use App\Service\Gateway;

class ControllerA extends BaseController
{
    /**
     * Validator
     *
     * @var Validator
     */
    private $validator;

    public function __construct()
    {
        $this->validator = new Validator();
    }

    public function foo(ServerRequestInterface $request, array $parameters)
    {
        $result = $this->getTemplater()->render('home', [
            'date' => (new \DateTime())->format('c'),
        ]);
        return $this->returnHtml($result);
    }

    public function newRecord(ServerRequestInterface $request, array $parameters)
    {
        $model = new Pyramid();
        $mapper = new Mapper();

        $mapped = $mapper->map($request->getQueryParams(), $model);
        if ($mapped) {
            $validationErrors = $this->validator->validatePyramid($model);
            if (!empty($validationErrors)) {
                $result = $this->getTemplater()->render('new_record', [
                    'model' => $model,
                    'error' => $validationErrors,
                ]);
                return $this->returnHtml($result);
            }
            $service = new Gateway('http://localhost:1323');
            if ($service->addPyramid($model)) {
                $model = new Pyramid();
            } else {
                $result = $this->getTemplater()->render('new_record', [
                    'model' => $model,
                    'error' => ['not saved'],
                ]);
                return $this->returnHtml($result);
            }
        }

        $result = $this->getTemplater()->render('new_record', [
            'model' => $model,
        ]);
        return $this->returnHtml($result);
    }

    public function getStat(ServerRequestInterface $request, array $parameters)
    {
        $queryParams = $request->getQueryParams();
        if (!isset($queryParams['class_name'])) {
            $result = $this->getTemplater()->render('select_class');
            return $this->returnHtml($result);
        }

        $service = new Gateway('http://localhost:1323');
        $data = $service->getStat($queryParams['class_name']);
        $result = $this->getTemplater()->render('show_stat', [
            'data' => $data,
        ]);
        return $this->returnHtml($result);
    }
}
