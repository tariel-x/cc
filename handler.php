<?php
use Psr\Http\Message\ServerRequestInterface;
use Symfony\Component\Routing\Matcher\UrlMatcher;
use Symfony\Component\Routing\RequestContext;
use Symfony\Component\Routing\RouteCollection;
use Symfony\Component\Routing\Route;
use Symfony\Component\Routing\Exception\ResourceNotFoundException;
use React\Http\Response;
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
        $response = callController($parameters, $request);
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

function callController(array $parameters, ServerRequestInterface $request)
{
    global $templater;
    $controllerString = $parameters['_controller'];
    unset($parameters['_controller']);
    unset($parameters['_route']);
    $controllerParts = explode("::", $controllerString);
    $controllerClass = $controllerParts[0];
    $controllerMethod = $controllerParts[1];
    $controller = new $controllerClass();
    $controller->setTemplater($templater);
    return $controller->$controllerMethod($request, $parameters);
}