<?php

require_once __DIR__ . '/../vendor/silex/autoload.php';

$app = new Silex\Application();

$app['autoloader']->registerNamespaceFallbacks(array(
    __DIR__ . '/../src',
));

$config = require __DIR__ . '/config.php';
foreach ($config as $key => $value) {
    $app[$key] = $value;
}

$app->register(new Silex\Provider\TwigServiceProvider(), array(
    'twig.path' => __DIR__ . '/templates',
    'twig.class_path' => __DIR__ . '/../vendor/twig/lib'
));

$app->register(new Silex\Provider\SessionServiceProvider());

$app->register(new Silex\Provider\UrlGeneratorServiceProvider());

$app->register(new Pegas\Cdn\CdnServiceProvider());

return $app;
