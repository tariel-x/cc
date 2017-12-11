<?php

namespace App;

class Templater
{
    /**
     * Template path
     *
     * @var string
     */
    private $path;

    public function __construct(string $path)
    {
        $this->path = $path;
    }

    public function render(string $template, array $params = [])
    {
        $file = $this->path . $template . ".php";
        $output = NULL;
        if (file_exists($file)) {
            extract($params);
            ob_start();
            include $file;
            $output = ob_get_clean();
        }
        return $output;
    }
}
