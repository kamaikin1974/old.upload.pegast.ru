<?php

namespace Pegas\Cdn;

abstract class Pool
{
    /**
     * @var \Cdn\Host[] hosts
     */
    protected $hosts = array();

    /**
     * Add CDN host into pool
     *
     * @param \Cdn\Host $host cdn host
     * @return void
     */
    public function addHost(Host $host)
    {
        $this->hosts[$host->getName()] = $host;
    }

    /**
     * Get host by name
     *
     * @throws \InvalidArgumentException
     * @param string $name host uniq name
     * @return \CDN\Host host
     */
    public function getHost($name)
    {
        if (!array_key_exists($name, $this->hosts)) {
            throw new \InvalidArgumentException(sprintf('Host "%s" not found in poll', $name));
        }

        return $this->hosts[$name];
    }

    /**
     * Select host
     *
     * @abstract
     * @return \Cdn\Host
     */
    abstract public function selectHost();
}
