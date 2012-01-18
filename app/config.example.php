<?php

return array(
    'debug' => false,

    'cdn.hosts' => array(
        new Pegas\Cdn\LocalHost(__DIR__ . '/../web/get', 'cdn.localhost', 'localhost-cdn'),
    ),
);
