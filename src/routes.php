<?php

use Slim\App;

return function (App $app) {
    $container = $app->getContainer();

    /********* ALL PUBLIC ROUTES *********/
    $app->group('/login', function () {
        $this->get('', 'Src\Controller\LoginController:login')->setName('login');
        $this->post('', 'Src\Controller\LoginController:login');
    });

    $app->get('/logout', 'Src\Controller\LoginController:logout')->setName('logout');

    $app->get('/error400', 'Src\Controller\ErrorController:error400')->setName('error_400');

                             /********* APP *********/
    /********* under csrfMiddleware & authenticationMiddleware *********/
    $app->group('/', function () use ($container) {
        $this->get('', 'Src\Controller\IndexController:index')->setName('index');
        $this->get('croissantage', 'Src\Controller\IndexController:listCroissantage')->setName('croissantage');
        $this->get('croissantageForVote', 'Src\Controller\IndexController:listForVote')->setName('list_for_vote');

        $this->group('voteCroissantage/{id}', function () {
            $this->get('', 'Src\Controller\IndexController:vote')->setName('vote_croissantage');
            $this->post('', 'Src\Controller\IndexController:vote');
        });

        $this->group('newCroissantage', function() {
            $this->get('', 'Src\Controller\IndexController:newCroissantage')->setName('new_croissantage');
            $this->post('', 'Src\Controller\IndexController:newCroissantage');
        });

        $this->group('myAccount', function () {
            $this->get('', 'Src\Controller\IndexController:modifyAccount')->setName('user_modify_account');
            $this->post('', 'Src\Controller\IndexController:modifyAccount');
        });

            /********* ADMIN ROUTES *********/
        /********* under adminMiddleware *********/
        $this->group('admin', function ()
        {
            $this->get('/', 'Src\Controller\AdminController:index')->setName('admin_index');
            $this->get('/listStudent', 'Src\Controller\AdminController:listStudents')->setName('admin_list_student');

            $this->group('/createStudent', function() {
                $this->get('', 'Src\Controller\AdminController:createStudent')->setName('admin_create_student');
                $this->post('', 'Src\Controller\AdminController:createStudent');
            });

            $this->group('/modifyStudent/{id}', function() {
               $this->get('', 'Src\Controller\AdminController:modifyStudent')->setName('admin_modify_student');
               $this->post('', 'Src\Controller\AdminController:modifyStudent');
            });

            $this->group('/updateRights', function() {
                $this->get('', 'Src\Controller\AdminController:updateRights')->setName('admin_update_rights');
                $this->post('', 'Src\Controller\AdminController:updateRights');
            });

        })->add($container->get('adminMiddleware'));
    })->add($container->get('csrfMiddleware'))->add($container->get('authenticationMiddleware'));
};
