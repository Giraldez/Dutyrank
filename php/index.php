<?php
require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../php/installer.php';
require_once __DIR__ . '/../php/loader.php';
require_once __DIR__ . '/../php/logConverter.php';

use Symfony\Component\HttpFoundation\Request;

//use Symfony\Component\HttpFoundation\ParameterBag;
//use Symfony\Component\HttpFoundation\Response;

$app = new Silex\Application();

$app['debug'] = true;

$app->before(function (Request $request) {
    if (0 === strpos($request->headers->get('Content-Type'), 'application/json')) {
        $data = json_decode($request->getContent(), true);
        $request->request->replace(is_array($data) ? $data : array());
    }
});

$app->get('/', function (Silex\Application $app) {
    $msg = "Bad route";
    return $app->json($msg);
});

$app->post('/install', function (Request $request, Silex\Application $app) {
    $password = $request->request->get('password');
    $installer = new installer();
    return $app->json($installer->install($password));
});

$app->get('/load', function (Silex\Application $app) {
    $loader = new loader();
    return $app->json($loader->fullLoad());
});

$app->post('/converter', function (Request $request, Silex\Application $app) {
    $password = $request->request->get('password');
    $logConverter = new logConverter();
    return $app->json($logConverter->convert($password));
});

$app->run();
