<?php


namespace Src\Controller;


use Psr\Container\ContainerInterface;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Log\LoggerInterface;
use Slim\Views\PhpRenderer;

class ErrorController extends BaseController
{
    public function __construct(PhpRenderer $view, LoggerInterface $logger, ContainerInterface $container)
    {
        parent::__construct($view, $logger, $container);
    }

    public function error400(RequestInterface $request, ResponseInterface $response)
    {
        return $this->render($response, 'errors/error400.phtml');
    }
}