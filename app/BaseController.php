<?php
namespace App;

use React\Http\Response;

class BaseController
{
    /**
     * Templater
     *
     * @var Templater
     */
    private $templater;

    /**
     * Set templater
     *
     * @param Templater $templater
     * @return void
     */
    public function setTemplater(Templater $templater)
    {
        $this->templater = $templater;
    }

    /**
     * Get templater
     *
     * @return Templater
     */
    public function getTemplater(): Templater
    {
        return $this->templater;
    }

    /**
     * Returns html response
     *
     * @param string $response
     * @param int $status
     * @return Response
     */
    protected function returnHtml(string $response, int $status = 200)
    {
        return new Response(
            $status,
            array('Content-Type' => 'text/html'),
            $response
        );
    }
}
