<?php

namespace Pegas\Cdn;

use Silex\Application;
use Silex\ControllerProviderInterface;
use Silex\ControllerCollection;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\Response;

class CdnApiControllerProvider implements ControllerProviderInterface
{
    public function connect(Application $app)
    {
        $controllers = new ControllerCollection();

        $controllers->put('/upload/{fileName}', function (Application $app, $fileName) {
            $file = new File(tempnam(sys_get_temp_dir(), 'CDN'));

            $openedFile = $file->openFile('w+');
            $openedFile->fwrite(file_get_contents('php://stdin'));

            $host = $app['cdn.pool']->selectHost();
            $fileId = $host->save($file);

            $url = $host->generateUrl($fileId, $fileName);

            return new Response($url, 302, array('Location' => $url));
        })->bind('api_upload');

        return $controllers;
    }
}
