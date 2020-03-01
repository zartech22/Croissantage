<?php


namespace Src\Utilities;


use Psr\Container\ContainerInterface;
use Src\Entities\Croissantage;

class HelperUtility
{
    /**
     * @var ContainerInterface
     */
    private $container;

    private $basePath;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
        $this->basePath = $this->container->get('request')->getUri()->getBasePath();
    }

    public function generateAssetPath($path)
    {
        $absolutePath = $this->basePath;

        if($path[0] !== '/')
            $absolutePath .= '/';

        $absolutePath .= $path;

        return $absolutePath;
    }

    public function getPathFor($route, array $args = [])
    {
        return $this->container->get('router')->pathFor($route, $args);
    }

    public function generateCsrfToken($tokenName)
    {
        try
        {
            $token = bin2hex(random_bytes(32));
        }
        catch (\Exception $e)
        {
            $token = uniqid('', true);
        }

        $tokenName = "csrf_$tokenName";

        $this->container->get('session')->set($tokenName, $token);

        return "<input type=\"hidden\" name=\"$tokenName\" value=\"$token\" />";
    }

    public function checkPostData(array $varNames) : bool
    {
        $valid = true;

        foreach ($varNames as $name)
        {
            if(!isset($_POST[$name]))
            {
                $valid = false;
                break;
            }
        }

        return $valid;
    }

    public function isAdmin()
    {
        $userId = $this->container->get('session')->get('userId');

        return ($userId !== false && $this->container->get('studentModel')->isAdmin($userId));
    }

    public function hasVoted(Croissantage $c) : bool
    {
        $voteModel = $this->container->get('voteModel');

        $userId = $this->container->get('session')->get('userId');
        $user = $this->container->get('studentModel')->find($userId);

        return $voteModel->hasVoted($c, $user);
    }
}