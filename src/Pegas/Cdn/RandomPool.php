<?php

namespace Pegas\Cdn;

class RandomPool extends Pool
{
    /**
     * Select random host
     *
     * @throws \RuntimeException
     * @return \Cdn\Host
     */
    public function selectHost()
    {
        if (count($this->hosts) < 1) {
            throw new \RuntimeException('No one CDN host added');
        }

        return $this->hosts[array_rand($this->hosts)];
    }
}
