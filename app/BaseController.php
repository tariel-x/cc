<?php
namespace App;

use Psr\Http\Message\ServerRequestInterface;
use React\Http\Response;

class BaseController
{
    /**
     * Request
     *
     * @var ServerRequestInterface
     */
    private $request;

    /**
     * Templater
     *
     * @var Templater
     */
    private $templater;

    public function __construct(ServerRequestInterface $request)
    {
        $this->request = $request;
    }

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
     * Get request
     *
     * @return ServerRequestInterface
     */
    protected function getRequest(): ServerRequestInterface
    {
        return $this->request;
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
