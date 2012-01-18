<?php

namespace Pegas\Cdn;

use Silex\Application;
use Silex\ServiceProviderInterface;

class CdnServiceProvider implements ServiceProviderInterface
{
    public function register(Application $app)
    {
        $app['cdn.pool'] = $app->share(function (Application $app) {
            $pool = new RandomPool();

            foreach ($app['cdn.hosts'] as $host) {
                $pool->addHost($host);
            }

            return $pool;
        });
    }
}
