<?php

namespace Pegas\Cdn;

use Symfony\Component\HttpFoundation\File\File;

interface Host
{
    /**
     * Save uploaded file on CDN host
     *
     * @abstract
     * @param \Symfony\Component\HttpFoundation\File\File $file file to save
     * @return string file id
     */
    public function save(File $file);

    /**
     * Generate url to uploaded file on CDN host
     *
     * @abstract
     * @param string $id file id
     * @param string $name file name
     * @return string url to file
     */
    public function generateUrl($id, $name);

    /**
     * Get host name for searching in pool
     *
     * @abstract
     * @return string host unique name
     */
    public function getName();
}
