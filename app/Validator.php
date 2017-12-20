<?php
namespace App;

use App\Model\Pyramid;

class Validator
{
    public function validatePyramid(Pyramid $model): array
    {
        $errors = [];
        $data = array_combine(range(1,40), array_fill(0,40,0));
        foreach (get_object_vars($model) as $key=>$item) {
            $matches = [];
            preg_match('/row(\d+)col(\d+)/', $key, $matches);
            if (count($matches) == 3) {
                if (empty((string)$model->{$key})) {
                    $errors[] = "empty pyramid value";
                }
                if (!isset($data[$model->{$key}])) {
                    $errors[] = sprintf("unrecognized value %d", $model->{$key});
                }
                $data[$model->{$key}]++;
            }
        }
        foreach ($data as $key=>$item) {
            if ($item !== 1) {
                $errors[] =  sprintf("%s found %d times", $key, $item);
            }
        }
        return $errors;
    }
}