<?php

namespace Controller;

use Silex\Application;

class Index
{
    public function __invoke(Application $app)
    {
        return $app['twig']->render('index.twig');
    }
}
