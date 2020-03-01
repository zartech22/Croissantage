<?php


namespace Src\Controller;

use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Log\LoggerInterface;
use Slim\Views\PhpRenderer;
use Src\Entities\Students;
use Src\Repositories\StudentRepository;

class LoginController extends BaseController
{
    private StudentRepository $studentModel;

    public function __construct(PhpRenderer $view, LoggerInterface $logger, ContainerInterface $container)
    {
        parent::__construct($view, $logger, $container);

        $this->studentModel = $this->container->get('studentModel');
    }

    public function login(Request $request, Response $response) : Response
    {
        if(isset($_POST['username']) && isset($_POST['password']))
        {
            /**
             * @var Students $student
             */
            $student = $this->studentModel->findByLogin($_POST['username']);

            if($student !== null && md5($_POST['password']) === $student->getPwd())
            {
                $session = $this->container->get('session');
                $session->set('userId', $student->getId());
                $session->set('isAdmin', $this->studentModel->isAdmin($student->getId()));

                if($session->has('redirectTo') === true)
                {
                    $resp = $this->redirectTo($session->get('redirectTo'), $response);

                    $session->unset('redirectTo');

                    return $resp;
                }
                else
                    return $this->redirectToRoute('index', $response);
            }
            else
                $this->container->get('session')->addFlash('loginFailed', true);
        }

        return $this->render($response, 'adminLTE_login.phtml', [
            'form_action' => $this->generateUrl('login')
        ]);
    }

    public function logout(Request $request, Response $response) : Response
    {
        $this->container->get('session')->destroy();

        return $this->redirectToRoute('login', $response);
    }
}