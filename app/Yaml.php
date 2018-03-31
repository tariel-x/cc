<?php
namespace App;

/**
 * Class Yaml
 * @package App
 * @author Nikita Gerasimov <tariel-x@ya.ru>
 */
class Yaml extends \Symfony\Component\Yaml\Yaml
{
    /**
     * Parses yaml and replaces env params
     * {@inheritdoc}
     */
    public static function parse(string $input, int $flags = 0)
    {
        $config = parent::parse($input, $flags);
        self::replaceRecursive($config);
        return $config;
    }

    /**
     * Recursive search for %env(BLA_BLA)%
     * @param array $items
     * @throws \Exception
     */
    private static function replaceRecursive(array &$items) {

        foreach ($items as &$item) {
            if (is_array($item)) {
                self::replaceRecursive($item);
            }
            if (is_string($item) && preg_match('/\%env\(([A-Z_]+)\)\%/u', $item, $matches)) {
                array_shift($matches);

                foreach ($matches as $match) {
                    $value = getenv($match);
                    if (empty($value)) {
                        throw new \Exception("Env " . $match . " not found.", 0);
                    }
                    $item = str_replace('%env('.$match.')%', $value, $item);
                }
            }
        }
    }
}