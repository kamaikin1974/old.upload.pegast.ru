<?php

namespace Pegas\Cdn;

use Silex\Application;
use Silex\ControllerProviderInterface;
use Silex\ControllerCollection;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\File\File;

class CdnControllerProvider implements ControllerProviderInterface
{
    public function connect(Application $app)
    {
        $download = function ($url, $infile) {
            $curl = curl_init($url);

            curl_setopt($curl, CURLOPT_TIMEOUT, 10);
            curl_setopt($curl, CURLOPT_FILE, fopen($infile->getRealPath(), 'w+'));

            return curl_exec($curl);
        };

        $controllers = new ControllerCollection();

        $controllers->get('/', function (Application $app) {
            return $app['twig']->render('index.html.twig');
        })->bind('home');

        $controllers->post('/upload', function (Application $app) {
            $file = $app['request']->files->get('cdn-file');

            if (!$file instanceof UploadedFile) {
                $app['session']->setFlash('upload-error', 'File not selected');
                return $app->redirect($app['url_generator']->generate('home'));
            }

            $host = $app['cdn.pool']->selectHost();
            $fileId = $host->save($file);

            $showFileParameters = array(
                'hostName' => $host->getName(),
                'fileId' => $fileId,
                'fileName' => $file->getClientOriginalName(),
            );

            return $app->redirect($app['url_generator']->generate('show_file', $showFileParameters));
        })->bind('upload');

        $controllers->post('/download', function (Application $app) use ($download) {
            $url = $app['request']->request->get('cdn-file');

            if (!preg_match('#^https?://.+/.+$#', $url)) {
                $app['session']->setFlash('download-error', 'Invalid url provided');
                $app['session']->setFlash('download-url', $url);
                return $app->redirect($app['url_generator']->generate('home'));
            }

            $tmp = new File(tempnam(sys_get_temp_dir(), 'CDN'));

            if (!$download($url, $tmp)) {
                $app['session']->setFlash('download-error', 'Can not download file');
                $app['session']->setFlash('download-url', $url);
                return $app->redirect($app['url_generator']->generate('home'));
            }

            $host = $app['cdn.pool']->selectHost();
            $fileId = $host->save($tmp);

            $showFileParameters = array(
                'hostName' => $host->getName(),
                'fileId' => $fileId,
                'fileName' => pathinfo($url, PATHINFO_BASENAME),
            );
            return $app->redirect($app['url_generator']->generate('show_file', $showFileParameters));
        })->bind('download');

        $controllers->get('/{hostName}/{fileId}/{fileName}', function (Application $app, $hostName, $fileId, $fileName) {
            $url = $app['cdn.pool']->getHost($hostName)->generateUrl($fileId, $fileName);

            return $app['twig']->render('show.html.twig', array(
                'url' => $url,
            ));
        })->bind('show_file');

        return $controllers;
    }
}
