<?php
namespace Src\Controller;

use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Log\LoggerInterface;
use Slim\Views\PhpRenderer;
use Src\Entities\Croissantage;
use Src\Repositories\StudentRepository;

final class IndexController extends BaseController
{
    /**
     * @var StudentRepository
     */
    private $studentModel;

    public function __construct(PhpRenderer $view, LoggerInterface $logger, ContainerInterface $container)
    {
        parent::__construct($view, $logger, $container);

        $this->studentModel = $this->container->get('studentModel');
    }

    public function index(Request $request, Response $response)
    {
        $this->logger->info("Index action dispatched");

        $this->render($response, 'index.phtml');

        return $response;
    }

    public function listCroissantage(Request $request, Response $response)
    {
        $c = $this->container->get('croissantageModel')->findAll();

        return $this->render($response, 'user/listCroissantage.phtml', [
            'croissantage' => $c
        ]);
    }

    public function listForVote(Request $request, Response $response)
    {
        $croissantage = $this->container->get('croissantageModel')->findAllForVote();

        return $this->render($response, 'user/listForVote.phtml', [
            'croissantage' => $croissantage
        ]);
    }

    public function vote(Request $request, Response $response, $args)
    {
        $pastries = $this->container->get('pastryModel')->findAllAvailable();

        if($request->getMethod() === 'POST' && $this->container->get('helper')->checkPostData(['pastry']))
        {
            $croissantage = $this->container->get('croissantageModel')->find($args['id']);
            $student = $this->getUser();
            $pastry = $this->container->get('pastryModel')->find($_POST['pastry']);

            if($pastry !== null && $croissantage !== null)
            {
                $this->container->get('voteModel')->persist($croissantage, $pastry, $student);
                $this->container->get('session')->addFlash('voteSuccess', true);

                return $this->redirectToRoute('list_for_vote', $response);
            }
        }

        return $this->render($response, 'user/vote.phtml', [
            'student' => $this->getUser(),
            'pastries' => $pastries,
            'croissantageId' => $args['id']
        ]);
    }

    public function newCroissantage(Request $request, Response $response)
    {
        if($request->getMethod() === 'POST')
        {
            $vars = ['croissanted', 'croissanter', 'date', 'dateCommand', 'dateDelivery'];
            $error = false;

            if($this->container->get('helper')->checkPostData($vars))
            {
                try
                {
                    $date = new \DateTime($_POST['date']);
                    $dateCommand = new \DateTime($_POST['dateCommand']);
                    $dateDelivery = new \DateTime($_POST['dateDelivery']);

                    $croissanted = $this->container->get('studentModel')->find($_POST['croissanted']);
                    $croissanter = $this->container->get('studentModel')->find($_POST['croissanter']);

                    if($date >= $dateCommand || $dateCommand >= $dateDelivery)
                        $error = 'Les dates ne correspondent pas...';
                    else if($croissanted === null || $croissanter === null)
                        $error = "Le croissanteur ou le croissanté n'existe pas...";
                    else
                    {
                        $croissantage = new Croissantage();

                        $croissantage->setIdCed($croissanted)
                            ->setIdCer($croissanter)
                            ->setDateC($date)
                            ->setDateCommand($dateCommand)
                            ->setDeadline($dateDelivery);

                        $this->container->get('croissantageModel')->persist($croissantage);

                        $this->container->get('session')->addFlash('newCroissantage', 'success');
                    }
                }
                catch(\Exception $e)
                {
                    $error = 'Erreur : format de date incorrect !';
                }

                if($error !== false)
                    $this->container->get('session')->addFlash('newCroissantage', $error);
            }
        }

        $students = $this->container->get('studentModel')->findAll();

        return $this->render($response, 'user/newCroissantage.phtml',[
            'students' => $students
        ]);
    }

    public function modifyAccount(Request $request, Response $response)
    {
        $user = $this->getUser();
        $pastryTypes = $this->container->get('pastryModel')->findAllAvailable();

        if($request->getMethod() === 'POST')
        {
            $varNames = ['username', 'alias', 'password', 'passwordBis', 'defaultPastry'];

            if($this->container->get('helper')->checkPostData($varNames))
            {
                $error = false;

                if(empty(trim($_POST['username'])))
                    $error = "Le nom d'utilisateur ne peut pas être vide !";
                else
                    $user->setLogin($_POST['username']);

                if(empty(trim($_POST['alias'])))
                    $error = "L'alias ne peut pas être vide !";
                else
                    $user->setAlias($_POST['alias']);

                if(!empty(trim($_POST['password'])))
                {
                    if($_POST['password'] === $_POST['passwordBis'])
                        $user->setRawPwd($_POST['password']);
                    else
                        $error = 'Les deux mots de passe ne concordent pas !';
                }

                if($error === false)
                {
                    $user->setDefaultPastry($_POST['defaultPastry']);

                    $this->container->get('studentModel')->update($user);

                    $this->container->get('session')->addFlash('adminStudentUpdate', 'success');
                }
                else
                    $this->container->get('session')->addFlash('adminStudentUpdate', $error);
            }
        }

        return $this->render($response, 'user/modifyStudent.phtml', [
            'student' => $user,
            'pastries' => $pastryTypes,
            'action' => $this->container->get('helper')->getPathFor('user_modify_account')
        ]);
    }
}