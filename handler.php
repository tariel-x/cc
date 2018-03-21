<?php
use Psr\Http\Message\ServerRequestInterface;
use Symfony\Component\Routing\Matcher\UrlMatcher;
use Symfony\Component\Routing\RequestContext;
use Symfony\Component\Routing\RouteCollection;
use Symfony\Component\Routing\Route;
use Symfony\Component\Routing\Exception\ResourceNotFoundException;
use React\Http\Response;
use React\Promise\Promise;
use App\Templater;

$templater = new Templater(__DIR__ . "/views/");

function handle (ServerRequestInterface $request)
{
    global $routes;

    $context = new RequestContext(
        '/', 
        $request->getMethod(),
        $request->getUri()->getHost(),
        $request->getUri()->getScheme(),
        $request->getUri()->getPort(),
        443,
        $request->getUri()->getPath(),
        $request->getUri()->getQuery()
    );
    $matcher = new UrlMatcher($routes, $context);
    try {
        $parameters = $matcher->match($request->getUri()->getPath());
    } catch (ResourceNotFoundException $exception) {
        return notFound();
    }

    try {
        $response = waitBodyEnd($parameters, $request);
    } catch (\Throwable $exception) {
        print $exception->getMessage();
        print $exception->getTraceAsString();
        return serverError();
    }
    
    return $response;
}

function notFound()
{
    return new Response(
        404,
        array('Content-Type' => 'text/html'),
        "not found"
    );
}

function serverError()
{
    return new Response(
        500,
        array('Content-Type' => 'text/html'),
        "server error"
    );
}

function waitBodyEnd(array $parameters, ServerRequestInterface $request)
{
    return new Promise(function ($resolve, $reject) use ($request, $parameters) {
        $contentLength = 0;
        $content = "";
        $request->getBody()->on('data', function ($data) use (&$contentLength, &$content) {
            $contentLength += strlen($data);
            $content .= $data;
        });
        $request->getBody()->on('end', function () use ($resolve, &$contentLength, &$content, &$request, $parameters){
            $response = callController($parameters, $request, $content);
            $resolve($response);
        });
        // an error occures e.g. on invalid chunked encoded data or an unexpected 'end' event
        $request->getBody()->on('error', function (\Exception $exception) use ($resolve, &$contentLength) {
            $response = serverError();
            $resolve($response);
        });
    });
}

function callController(array $parameters, ServerRequestInterface $request, string $body)
{
    printf("New request %s %s%s\n", $request->getMethod(), $request->getUri()->getPath(), $request->getUri()->getQuery());
    global $templater;
    global $jsonrpc;
    $controllerString = $parameters['_controller'];
    unset($parameters['_controller']);
    unset($parameters['_route']);
    $controllerParts = explode("::", $controllerString);
    $controllerClass = $controllerParts[0];
    $controllerMethod = $controllerParts[1];
    $controller = new $controllerClass($jsonrpc);
    $controller->setTemplater($templater);
    return $controller->$controllerMethod($request, $parameters, $body);
}