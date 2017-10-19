<?php

use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

require 'vendor/autoload.php';

$config = [
	'displayErrorDetails' => true,
	'determineRouteBeforeAppMiddleware' => true,
];

$app = new \Slim\App(["settings" => $config]);
$container = $app->getContainer();

$container['db'] = function ($c) {return true;};
$container['view'] = function ($c) {
    $view = new \Slim\Views\Twig('views', [
        'cache' => false
    ]);

    // Instantiate and add Slim specific extension
    $basePath = rtrim(str_ireplace('index.php', '', $c['request']->getUri()->getBasePath()), '/');
    $view->addExtension(new \Slim\Views\TwigExtension($c['router'], $basePath));

    return $view;
};

$container['SessCtrl'] = function ($container) {
	return new \Lib\Controllers\SessCtrl($container);
};
$container['AuthCtrl'] = function ($container) {
	return new \Lib\Controllers\AuthCtrl($container);
};

$app->add(new Lib\Middlewares\SessMiddleware);

# Routes
$app->get('/login', 'AuthCtrl:login');
$app->get('/logout', 'AuthCtrl:logout');
$app->post('/check-login', 'AuthCtrl:checkLogin');
$app->get('/', 'SessCtrl:index');

$app->get('/ajax', 'SessCtrl:ajax');

$app->run();