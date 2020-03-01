<?php
namespace Src\Controller;

use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Log\LoggerInterface;
use Slim\Interfaces\RouterInterface;
use Slim\Views\PhpRenderer;
use Src\Entities\Students;
use Src\Utilities\SessionUtility;

class BaseController
{
    /**
     * @var PhpRenderer
     */
    protected PhpRenderer $view;

    /**
     * @var LoggerInterface
     */
    protected LoggerInterface $logger;

    /**
     * @var ContainerInterface
     */
    protected ContainerInterface $container;

    /**
     * @var RouterInterface
     */
    private RouterInterface $router;

    public function __construct(PhpRenderer $view, LoggerInterface $logger, ContainerInterface $container)
    {
        $this->view = $view;
        $this->logger = $logger;
        $this->container = $container;

        $this->router = $this->container->get('router');
    }

    protected function generateUrl(string $route) : string
    {
        return $this->router->pathFor($route);
    }

    protected function redirectTo(string $path, ResponseInterface $response, int $statusCode = 303) : ResponseInterface
    {
        return $response->withHeader('Location', $path)->withStatus($statusCode);
    }

    protected function redirectToRoute(string $route, ResponseInterface $response, int $statusCode = 302) : ResponseInterface
    {
        $path = $this->router->pathFor($route);

        return $this->redirectTo($path, $response, $statusCode);
    }

    protected function render(ResponseInterface $response, string $template, array $data = []) : ResponseInterface
    {
        $this->sanitizeData($data);

        $this->addHelper($data);
        $this->addUser($data);

        return $this->view->render($response, $template, $data);
    }

    protected function getUser() : ?Students
    {
        $user = null;

        $session = $this->container->get('session');

        if($session->has('userId'))
        {
            $id = $session->get('userId');

            $user = $this->container->get('studentModel')->find($id);
        }

        return $user;
    }

    private function sanitizeData(array &$data) : void
    {
        foreach ($data as $key => &$elem)
        {
            if(is_string($elem))
                $data[$key] = htmlspecialchars($elem);
            else if(is_array($elem))
                $this->sanitizeData($elem);
        }
    }

    private function addHelper(array &$data) : void
    {
        $helper = $this->container->get('helper');

        // Helper to get url from a route name
        $data['_url'] = [$helper, 'getPathFor'];

        // Helper to get full path for an asset
        $data['_asset'] = [$helper, 'generateAssetPath'];

        // Helper to create an register a csrf token
        $data['_csrf'] = [$helper, 'generateCsrfToken'];

        // Helper saying if we are admin or not
        $data['_isAdmin'] = [$helper, 'isAdmin'];

        // Expose the session for error messages
        $data['_session'] = $this->container->get('session');

        // Tells if the current user has voted for a particular croissantage
        $data['_hasVoted'] = [$helper, 'hasVoted'];
    }

    private function addUser(array &$data) : void
    {
        /**
         * @var SessionUtility $session
         */
        $session = $this->container->get('session');

        if($session->has('userId') === true)
            $data['user'] = $this->container->get('studentModel')->find($session->get('userId'));
    }
}