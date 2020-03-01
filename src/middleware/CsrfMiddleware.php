<?php


namespace Src\Middleware;


use Psr\Container\ContainerInterface;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

class CsrfMiddleware
{
    private $container;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    public function __invoke(RequestInterface $request, ResponseInterface $response, $next)
    {
        if($request->getMethod() === 'POST' && !$this->validateToken($_POST))
        {
            $indexPath = $this->container->get('router')->pathFor('error_400');
            return $response->withHeader('Location', $indexPath);
        }
        else
            return $next($request, $response);
    }

    private function validateToken(array $data)
    {
        $isValid = true;

        $tokenName = $this->findToken($data);

        if($tokenName !== false)
        {
            $session = $this->container->get('session');

            // Token OK
            if($session->has($tokenName) && $session->get($tokenName) === $data[$tokenName])
                $session->unset($tokenName);
            else
                $isValid = false;
        }

        return $isValid;
    }

    private function findToken(array $data)
    {
        $tokenKey = false;

        foreach ($data as $key => $value)
        {
            if(strpos($key, 'csrf') === 0)
            {
                $tokenKey = $key;
                break;
            }
        }

        return $tokenKey;
    }
}