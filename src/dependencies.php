<?php

use Slim\App;

return function (App $app) {
    $container = $app->getContainer();

    // view renderer
    $container['renderer'] = function ($c) {
        $settings = $c->get('settings')['renderer'];
        return new \Slim\Views\PhpRenderer($settings['template_path']);
    };

    // monolog
    $container['logger'] = function ($c) {
        $settings = $c->get('settings')['logger'];
        $logger = new \Monolog\Logger($settings['name']);
        $logger->pushProcessor(new \Monolog\Processor\UidProcessor());
        $logger->pushHandler(new \Monolog\Handler\StreamHandler($settings['path'], $settings['level']));
        return $logger;
    };

    // PDO
    $container['pdo'] = function ($container) {
        $settings = $container->get('settings');
        $dsn = 'mysql:host=' .$settings['db']['host']. ';dbname=' .$settings['db']['dbname']. ';port=' .$settings['db']['port'];
        $pdo = new PDO($dsn, $settings['db']['user'], $settings['db']['pass']);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $pdo->setAttribute(PDO::ATTR_EMULATE_PREPARES, false); // Disable emulate prepared statements
        $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC); // Set default fetch mode
        return $pdo;
    };

    // -----------------------------------------------------------------------------
    // Utility factories
    // -----------------------------------------------------------------------------
    $container['session'] = function($container) {
        return new \Src\Utilities\SessionUtility();
    };

    $container['helper'] = function ($container) {
        return new \Src\Utilities\HelperUtility($container);
    };

    // -----------------------------------------------------------------------------
    // Model factories
    // -----------------------------------------------------------------------------
    $container['cfgModel'] = function ($container) {
        $settings = $container->get('settings');
        $cfgModel = new Src\Model\ConfigurationModel($container->get('pdo'));
        return $cfgModel;
    };

    $container['appModel'] = function($container) {
        $appModel = new Src\Models\AppModel($container->get('pdo'));

        return $appModel;
    };

    $container['rightsModel'] = function ($container) {
        return new \Src\Repositories\RightsRepository($container->get('pdo'));
    };

    $container['pastryModel'] = function ($container) {
        return new \Src\Repositories\PastryTypeRepository($container->get('pdo'));
    };

    $container['studentModel'] = function ($container) {
        $pdo = $container->get('pdo');
        $pastryModel = $container->get('pastryModel');
        $rightsModel = $container->get('rightsModel');

        return new \Src\Repositories\StudentRepository($pdo, $pastryModel, $rightsModel);
    };

    $container['croissantageModel'] = function($container) {
        $pdo = $container->get('pdo');
        $studentModel = $container->get('studentModel');

        return new \Src\Repositories\CroissantageRepository($pdo, $studentModel);
    };

    // -----------------------------------------------------------------------------
    // Middleware factories
    // -----------------------------------------------------------------------------
    // Authentication middleware
    $container['authenticationMiddleware'] = function($container) {
        return new Src\Middleware\AuthenticationMiddleware($container);
    };

    $container['csrfMiddleware'] = function ($container) {
        return new \Src\Middleware\CsrfMiddleware($container);
    };

    $container['adminMiddleware'] = function ($container) {
        $model = $container->get('studentModel');
        $router = $container->get('router');
        $session = $container->get('session');

        return new \Src\Middleware\AdminMiddleware($model, $router, $session);
    };

    // -----------------------------------------------------------------------------
    // Controller factories
    // -----------------------------------------------------------------------------

    $container['Src\Controller\IndexController'] = function ($container) {
        $view = $container->get('renderer');
        $logger = $container->get('logger');

        return new Src\Controller\IndexController($view, $logger, $container);
    };

    $container['Src\Controller\LoginController'] = function ($container) {
        $view = $container->get('renderer');
        $logger = $container->get('logger');

        return new \Src\Controller\LoginController($view, $logger, $container);
    };

    $container['Src\Controller\AdminController'] = function ($container) {
        $view = $container->get('renderer');
        $logger = $container->get('logger');

        return new \Src\Controller\AdminController($view, $logger, $container);
    };

    $container['Src\Controller\ErrorController'] = function ($container) {
        $view = $container->get('renderer');
        $logger = $container->get('logger');

        return new \Src\Controller\ErrorController($view, $logger, $container);
    };
};