<?php

return array(
    'debug' => false,

    'cdn.hosts' => array(
        new Cdn\LocalHost(__DIR__ . '/../web/get', 'cdn.localhost', 'localhost-cdn'),
    ),
);
