<?php

namespace Controller;

use Symfony\Component\HttpFoundation\File\File;

use Silex\Application;

class Download extends PoolController
{
    public function __invoke(Application $app)
    {
        $url = $app['request']->request->get('cdn-file');

        if (!preg_match('#^https?://.+/.+$#', $url)) {
            $app['session']->setFlash('download-error', 'Invalid url provided');
            $app['session']->setFlash('download-url', $url);
            return $app->redirect($app['url_generator']->generate('home'));
        }

        $tmp = new File(tempnam(sys_get_temp_dir(), 'CDN'));

        if (!$this->downloadFile($url, $tmp)) {
            $app['session']->setFlash('download-error', 'Can not download file');
            $app['session']->setFlash('download-url', $url);
            return $app->redirect($app['url_generator']->generate('home'));
        }

        $host = $this->pool->selectHost();
        $fileId = $host->save($tmp);

        $showFileParameters = array(
            'hostName' => $host->getName(),
            'fileId' => $fileId,
            'fileName' => pathinfo($url, PATHINFO_BASENAME)
        );
        return $app->redirect($app['url_generator']->generate('show_file', $showFileParameters));
    }

    protected function downloadFile($url, File $infile)
    {
        $curl = curl_init($url);

        curl_setopt($curl, CURLOPT_TIMEOUT, 10);
        curl_setopt($curl, CURLOPT_FILE, fopen($infile->getRealPath(), 'w+'));

        return curl_exec($curl);
    }
}
