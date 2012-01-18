<?php

require_once __DIR__ . '/../vendor/silex/autoload.php';

$app = new Silex\Application();

$app['autoloader']->registerNamespaceFallbacks(array(
    __DIR__ . '/../src',
));

$app->register(new Silex\Extension\TwigExtension(), array(
    'twig.path' => __DIR__ . '/templates',
    'twig.class_path' => __DIR__ . '/../vendor/twig/lib'
));

$app->register(new Silex\Extension\SessionExtension());

$app->register(new Silex\Extension\UrlGeneratorExtension());

$hosts = require_once __DIR__ . '/hosts.php';

$pool = new Cdn\RandomPool();
foreach ($hosts as $host) {
    $pool->addHost($host);
}

return $app;
