<?php

namespace App\Service;

class Helper
{
    /**
     * Recursive array sort
     * @param $data
     */
    public function sort(&$data)
    {
        if (!is_array($data)) {
            return;
        }
        foreach ($data as &$value) {
            $this->sort($value);
        }
        ksort($data);
    }

    /**
     * Make structure-independence array hash
     * @param array $data
     * @return string
     */
    public function hash(array $data): string
    {
        $this->sort($data);
        return md5(json_encode($data));
    }

    /**
     * Check that search array is in list
     * @param array $list
     * @param array $search
     * @return bool
     */
    public function arrayContainsArray(array $list, array $search): bool
    {
        $newHash = $this->hash($search);
        $hashes = array_map(function (array $service) {
            return $this->hash($service);
        }, $list);

        if (in_array($newHash, $hashes)) {
            return true;
        }
        return false;
    }
}