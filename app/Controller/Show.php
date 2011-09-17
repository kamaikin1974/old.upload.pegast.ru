<?php

namespace Controller;

use Symfony\Component\HttpFoundation\File\UploadedFile;

use Silex\Application;

class Show extends PoolController
{
    public function __invoke(Application $app, $hostName, $fileId, $fileName)
    {
        $url = $this->pool->getHost($hostName)->generateUrl($fileId, $fileName);

        return $app['twig']->render('show.twig', array(
            'url' => $url
        ));
    }
}
