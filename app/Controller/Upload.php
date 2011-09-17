<?php

namespace Controller;

use Symfony\Component\HttpFoundation\File\UploadedFile;

use Silex\Application;

class Upload extends PoolController
{
    public function __invoke(Application $app)
    {
        $file = $app['request']->files->get('cdn-file');

        if (!$file instanceof UploadedFile) {
            $app['session']->setFlash('error', 'File not selected');
            return $app->redirect($app['url_generator']->generate('home'));
        }

        $host = $this->pool->selectHost();
        $fileId = $host->save($file);

        $showFileParameters = array(
            'hostName' => $host->getName(),
            'fileId' => $fileId,
            'fileName' => $file->getClientOriginalName()
        );
        return $app->redirect($app['url_generator']->generate('show_file', $showFileParameters));
    }
}
