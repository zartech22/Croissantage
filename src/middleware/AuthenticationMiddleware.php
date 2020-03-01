<?php

namespace Src\Middleware;

use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class AuthenticationMiddleware
{
    /**
     * @var ContainerInterface
     */
    private ContainerInterface $container;

    public function __construct($container)
    {
        $this->container = $container;
    }

    public function __invoke(ServerRequestInterface $request, ResponseInterface $response, $next)
    {
        if($this->container->get('session')->has('userId'))
            return $next($request, $response);
        else
        {
            $loginPath = $this->container->get('router')->pathFor('login');

            if($request->getUri()->getPath() !== '/')
                $this->container->get('session')->set('redirectTo', $request->getUri()->getPath());

            return $response->withHeader('Location', $loginPath)->withStatus(303);
        }
    }
}