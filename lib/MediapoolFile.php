<?php

use Sabre\DAV;

class MediapoolFile extends DAV\File {

    private $myPath;

    function __construct($myPath) {

        $this->myPath = $myPath;

    }

    function getName() {
        $media = $this->mediaForPath($this->myPath);
        return $media->getFileName();
    }

    function get() {
        $media = $this->mediaForPath($this->myPath);
        return fopen(rex_path::media($media->getFileName()),'r');
    }

    function getSize() {
        $media = $this->mediaForPath($this->myPath);
        return $media->getSize();
    }

    function getETag() {
        $media = $this->mediaForPath($this->myPath);

        return '"' . md5_file(rex_path::media($media->getFileName())) . '"';
    }

    /**
     * @param $path
     * @return null|rex_media
     */
    private function mediaForPath($path)
    {
        $parts = explode('/', $path);

        // our medianame is already unique
        $mediaName = array_pop($parts);

        return rex_media::get($mediaName);
    }
}