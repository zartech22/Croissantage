<?php


namespace Src\Controller;


use Psr\Container\ContainerInterface;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Log\LoggerInterface;
use Slim\Views\PhpRenderer;
use Src\Entities\Students;

class AdminController extends BaseController
{
    public function __construct(PhpRenderer $view, LoggerInterface $logger, ContainerInterface $container)
    {
        parent::__construct($view, $logger, $container);
    }

    public function index(RequestInterface $request, ResponseInterface $response, $args)
    {
        $studentId = $this->container->get('session')->get('userId');
        $student = $this->container->get('studentModel')->find($studentId);

        $this->render($response, 'index.phtml', [
            'student' => $student,
        ]);

        return $response;
    }

    public function listStudents(RequestInterface $request, ResponseInterface $response)
    {
        $students = $this->container->get('studentModel')->findAll();

        return $this->render($response, 'admin/listStudents.phtml', compact('students'));
    }

    public function createStudent(RequestInterface $request, ResponseInterface $response)
    {
        if($request->getMethod() === 'POST')
        {
            $varNames = ['username', 'alias', 'password', 'passwordBis'];

            if($this->container->get('helper')->checkPostData($varNames))
            {
                if($_POST['password'] === $_POST['passwordBis'])
                {
                    $student = Students::createStudent($_POST['username'], $_POST['alias'], $_POST['password']);

                    if($student !== null)
                        $this->container->get('studentModel')->persist($student);
                }
            }
        }

        return $this->render($response, 'admin/createStudent.phtml');
    }

    public function modifyStudent(RequestInterface $request, ResponseInterface $response, array $args)
    {
        /** @var Students $student */
        $student = $this->container->get('studentModel')->find($args['id']);
        $pastryTypes = $this->container->get('pastryModel')->findAllAvailable();
        $formAction = $this->container->get('helper')->getPathFor('admin_modify_student', ['id' => $args['id']]);

        if($request->getMethod() === 'POST')
        {
            $varNames = ['username', 'alias', 'password', 'passwordBis', 'defaultPastry'];

            if($this->container->get('helper')->checkPostData($varNames))
            {
                $error = false;

                if(empty(trim($_POST['username'])))
                    $error = "Le nom d'utilisateur ne peut pas être vide !";
                else
                    $student->setLogin($_POST['username']);

                if(empty(trim($_POST['alias'])))
                    $error = "L'alias ne peut pas être vide !";
                else
                    $student->setAlias($_POST['alias']);

                if(!empty(trim($_POST['password'])))
                {
                    if($_POST['password'] === $_POST['passwordBis'])
                        $student->setRawPwd($_POST['password']);
                    else
                        $error = 'Les deux mots de passe ne concordent pas !';
                }

                if($error === false)
                {
                    $student->setDefaultPastry($_POST['defaultPastry']);

                    $this->container->get('studentModel')->update($student);

                    $this->container->get('session')->addFlash('adminStudentUpdate', 'success');
                }
                else
                    $this->container->get('session')->addFlash('adminStudentUpdate', $error);

                return $this->render($response, 'user/modifyStudent.phtml', [
                    'student' => $student,
                    'pastries' => $pastryTypes,
                    'action' => $formAction
                ]);
            }
        }
        else if($student !== null)
            return $this->render($response, 'user/modifyStudent.phtml', [
                'student' => $student,
                'pastries' => $pastryTypes,
                'action' => $formAction
            ]);
        else
            return $this->redirectToRoute('index', $response);
    }

    public function updateRights(RequestInterface $request, ResponseInterface $response)
    {
        if($request->getMethod() === 'POST' && $this->container->get('helper')->checkPostData(['rights']))
        {
            foreach ($_POST['rights'] as $studentId => $roleId)
                $this->container->get('rightsModel')->updateRightByStudent($studentId, $roleId);

            $this->container->get('session')->addFlash('rightUpdateSuccess', true);
        }

        $students = $this->container->get('studentModel')->findAllWithRights();
        $roles = $this->container->get('rightsModel')::ROLES;

        $this->render($response, 'admin/updateRights.phtml', [
            'studentsAndRights' => $students,
            'roles' => $roles
        ]);
    }
}