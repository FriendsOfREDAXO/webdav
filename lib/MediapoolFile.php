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

    function getLastModified()
    {
        $media = $this->mediaForPath($this->myPath);
        return $media->getUpdateDate();
    }

    function getETag() {
        $media = $this->mediaForPath($this->myPath);

        return '"' . md5_file(rex_path::media($media->getFileName())) . '"';
    }

    /**
     * Delete the current file
     *
     * @return void
     */
    function delete() {
        $media = $this->mediaForPath($this->myPath);
        $result = rex_mediapool_deleteMedia($media->getFileName());

        if (!$result['ok']) {
            throw new DAV\Exception\Forbidden($result['msg']);
        }
    }

    /**
     * Returns the mime-type for a file
     *
     * If null is returned, we'll assume application/octet-stream
     *
     * @return mixed
     */
    function getContentType() {
        $media = $this->mediaForPath($this->myPath);

        if ($media->getType()) {
            return $media->getType();
        }

        return null;

    }

    /**
     * @param $path
     * @return rex_media
     */
    private function mediaForPath($path)
    {
        $parts = explode('/', $path);

        // our medianame is already unique
        $mediaName = array_pop($parts);
        $media = rex_media::get($mediaName);

        if (!$media) {
            throw new DAV\Exception\NotFound('Unable to find media with name "'. $mediaName .'"');
        }

        return $media;
    }
}