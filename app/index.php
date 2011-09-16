<?php

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\File\UploadedFile;

require_once __DIR__ . '/../vendor/silex/autoload.php';

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
    $file = $app['request']->files->get('cdn_file');

    if (!$file instanceof UploadedFile) {
        $app['session']->setFlash('error', 'File not selected');
        return $app->redirect('/');
    }

    $hash = hash_file('sha256', $file->getPath());
    $extension = pathinfo($file->getClientOriginalName(), PATHINFO_EXTENSION);
    $name = sprintf("%s.%s", $hash, $extension);

    $file->move(__DIR__ . '/../web/get', $name);

    return $app->redirect('/show/' . $name);
});

$app->get('/show/{link}', function (Silex\Application $app, $link) {
    return $app['twig']->render('show.twig', array(
        'link' => $link
    ));
})
->convert('link', function ($name) use ($app) {
    return $app['url_generator']->generate('get_file', array('file' => $name), true);
});

$app->get('/get/{file}', function (File $file) {
    $response = new Response(file_get_contents($file->getRealPath()));
    $response->headers->set('Content-Type', $file->getMimeType());

    return $response;
})
->bind('get_file')
->convert('file', function ($name) {
    return new File(__DIR__ . '/../web/get/' . $name);
});


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
