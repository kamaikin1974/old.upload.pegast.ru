<?php

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\File\UploadedFile;

require_once __DIR__ . '/../vendor/silex/autoload.php';

function uploaded_file_mapper_name($hash) {
    return substr($hash, 0, 2) . '/' . substr($hash, 2, 2) . '/' . substr($hash, 4);
}

function uploaded_file_mapper_path($hash) {
    return __DIR__ . '/../web/get/' . uploaded_file_mapper_name($hash);
}

function get_cdn_url($hash, $name) {
    return sprintf('http://cdn01.pegast.su/get/%s/%s', uploaded_file_mapper_name($hash), $name);
}

$app = new Silex\Application();

$app->register(new Silex\Extension\TwigExtension(), array(
    'twig.path' => __DIR__ . '/../views',
    'twig.class_path' => __DIR__ . '/../vendor/silex/vendor/twig/lib'
));
$app->register(new Silex\Extension\SessionExtension());
$app->register(new Silex\Extension\UrlGeneratorExtension());


$app->get('/', function (Silex\Application $app) {
    return $app['twig']->render('index.twig');
});

$app->post('/upload', function (Silex\Application $app)  {
    $file = $app['request']->files->get('cdn-file');

    if (!$file instanceof UploadedFile) {
        $app['session']->setFlash('error', 'File not selected');
        return $app->redirect('/');
    }

    $hash = hash_file('sha256', $file->getPath());

    $directory = uploaded_file_mapper_path($hash);
    $filename = $file->getClientOriginalName();

    $file->move($directory, $filename);

    return $app->redirect($app['url_generator']->generate('show_file', array('hash' => $hash, 'name' => $filename)));
});

$app->get('/show/{hash}/{name}', function (Silex\Application $app, $hash, $name) {
    $url = get_cdn_url($hash, $name);

    return $app['twig']->render('show.twig', array(
        'url' => $url
    ));
})->bind('show_file');

$app->error(function (\Exception $e, $code) {
    switch ($code) {
        case 404:
            $message = 'The requested page could not be found.';
            break;
        default:
            $message = $e->getMessage();
    }

    return new Response($message, $code);
});

$app->run();
