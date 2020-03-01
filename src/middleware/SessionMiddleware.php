<?php


namespace Src\Middleware;


use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Src\Utilities\SessionUtility;

class SessionMiddleware
{
    private $session;

    public function __construct(SessionUtility $session)
    {
        $this->session = $session;
    }

    public function __invoke(ServerRequestInterface $request, ResponseInterface $response, $next)
    {
        $this->session->start();

        return $next($request, $response);
    }
}