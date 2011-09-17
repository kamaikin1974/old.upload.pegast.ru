<?php

namespace Cdn;

use Symfony\Component\HttpFoundation\File\File;

class LocalHost implements Host
{
    /**
     * @var string path to uploads
     */
    protected $directory;

    /**
     * @var string cdn hostname
     */
    protected $hostname;

    /**
     * @var string uniq host name
     */
    protected $name;

    /**
     * @param string $directory path to uploads
     * @param string $hostname cdn hostname
     * @param string $name uniq host name
     */
    public function __construct($directory, $hostname, $name)
    {
        $this->directory = $directory;
        $this->hostname = $hostname;
        $this->name = $name;
    }

    /**
     * Save uploaded file on CDN host
     *
     * @param \Symfony\Component\HttpFoundation\File\File $file file to save
     * @return string file hash
     */
    public function save(File $file)
    {
        $hash = hash_file('sha256', $file->getPathname());

        $path = $this->generatePath($hash);

        $file->move(pathinfo($path, PATHINFO_DIRNAME), pathinfo($path, PATHINFO_FILENAME));

        return $hash;
    }

    /**
     * Generate url to uploaded file on CDN host
     *
     * @param string $hash file hash
     * @param string $name file name
     * @return string url to file
     */
    public function generateUrl($hash, $name)
    {
        return sprintf('http://%s/get/%s/%s', $this->hostname, $this->breakHash($hash), $name);
    }

    /**
     * Get host name for searching in pool
     *
     * @return string host unique name
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Break hash to subfolders
     *
     * @param string $hash file hash
     * @return string breaked hash
     */
    protected function breakHash($hash)
    {
        return substr($hash, 0, 2) . DIRECTORY_SEPARATOR
             . substr($hash, 2, 2) . DIRECTORY_SEPARATOR
             . substr($hash, 4, 2) . DIRECTORY_SEPARATOR
             . substr($hash, 6);
    }

    /**
     * Generate path to result file
     *
     * @param string $hash file hash
     * @return string path to result file
     */
    protected function generatePath($hash)
    {
        return $this->directory . DIRECTORY_SEPARATOR . $this->breakHash($hash);
    }
}
