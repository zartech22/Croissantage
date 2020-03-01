<?php


namespace Src\Middleware;


use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Slim\Interfaces\RouterInterface;
use Src\Repositories\StudentRepository;
use Src\Utilities\SessionUtility;

class AdminMiddleware
{
    private StudentRepository $studentModel;
    private RouterInterface $router;
    private SessionUtility $session;

    public function __construct(StudentRepository $model, RouterInterface $router, SessionUtility $sessionUtility)
    {
        $this->studentModel = $model;
        $this->router = $router;
        $this->session = $sessionUtility;
    }

    public function __invoke(RequestInterface $request, ResponseInterface $response, callable $next)
    {
        $userId = $this->session->get('userId');

        if($this->studentModel->isAdmin($userId))
            return $next($request, $response);
        else
            return $response->withHeader('Location', $this->router->pathFor('index'));
    }
}