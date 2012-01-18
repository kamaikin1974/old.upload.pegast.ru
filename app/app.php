<?php

$app = require_once __DIR__ . '/bootstrap.php';

// Configure routes
$app->get('/', new Controller\Index())->bind('home');
$app->post('/upload', new Controller\Upload($pool))->bind('upload');
$app->post('/download', new Controller\Download($pool))->bind('download');
$app->get('/{hostName}/{fileId}/{fileName}', new Controller\Show($pool))->bind('show_file');
$app->error(new Controller\Error());

return $app;
