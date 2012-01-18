<?php

$app = require_once __DIR__ . '/bootstrap.php';

$app->mount('/', new Pegas\Cdn\CdnControllerProvider());

$app->error(function (\Exception $e, $code) {
    switch ($code) {
        case 404:
            $message = 'The requested page could not be found.';
            break;
        default:
            $message = $e->getMessage();
    }

    return new Symfony\Component\HttpFoundation\Response($message, $code);
});

return $app;
