<?php

require_once __DIR__ . '/../vendor/silex/autoload.php';

// Start application
$app = new Silex\Application();

$app['autoloader']->registerNamespace('Cdn', __DIR__);
$app['autoloader']->registerNamespace('Controller', __DIR__);

$app->register(new Silex\Extension\TwigExtension(), array(
    'twig.path' => __DIR__ . '/../views',
    'twig.class_path' => __DIR__ . '/../vendor/silex/vendor/twig/lib'
));
$app->register(new Silex\Extension\SessionExtension());
$app->register(new Silex\Extension\UrlGeneratorExtension());


// Configure pool
$hosts = require_once __DIR__ . '/hosts.php';

$pool = new Cdn\RandomPool();
foreach ($hosts as $host) {
    $pool->addHost($host);
}


// Configure routes
$app->get('/', new Controller\Index())->bind('home');
$app->post('/upload', new Controller\Upload($pool))->bind('upload');
$app->get('/{hostName}/{fileId}/{fileName}', new Controller\Show($pool))->bind('show_file');
$app->error(new Controller\Error());


// Run application
$app->run();
