<?php

namespace Controller;

use Cdn\Pool;

abstract class PoolController
{
    /**
     * @var \Cdn\Pool pool
     */
    protected $pool;

    public function __construct(Pool $pool)
    {
        $this->pool = $pool;
    }
}
