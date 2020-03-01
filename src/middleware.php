<?php

use Slim\App;

return function (App $app) {
    $app->add(new \Src\Middleware\SessionMiddleware($app->getContainer()->get('session')));
    // e.g: $app->add(new \Slim\Csrf\Guard);
};
