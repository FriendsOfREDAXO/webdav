<?php

use Sabre\DAV;

class MediapoolRoot extends DAV\Collection
{
    function getChildren()
    {
        $children = array();

        foreach (rex_media_category::getRootCategories() as $rootCategory) {
            $path = '/' . $rootCategory->getName();
            $children[] = new MediapoolDirectory($path);
        }

        // method was added with redaxo 5.3
        if (method_exists(rex_media::class, 'getRootMedia')) {
            foreach(rex_media::getRootMedia() as $rootMedia) {
                $path = '/' . $rootMedia->getFileName();
                $children[] = new MediapoolFile($path);
            }
        }

        return $children;
    }

    function getChild($name)
    {
        foreach (rex_media_category::getRootCategories() as $rootCategory) {
            if ($rootCategory->getName() == $name) {
                return new MediapoolDirectory('/'. $name);
            }
        }

        // method was added with redaxo 5.3
        if (method_exists(rex_media::class, 'getRootMedia')) {
            foreach(rex_media::getRootMedia() as $rootMedia) {
                if ($rootMedia->getFileName() == $name) {
                    return new MediapoolFile('/'. $name);
                }
            }
        }

        throw new DAV\Exception\NotFound('The file with name: '. $name . ' could not be found');
    }

    function childExists($name)
    {
        foreach (rex_media_category::getRootCategories() as $rootCategory) {
            if ($rootCategory->getName() == $name) {
                return true;
            }
        }

        // method was added with redaxo 5.3
        if (method_exists(rex_media::class, 'getRootMedia')) {
            foreach(rex_media::getRootMedia() as $rootMedia) {
                if ($rootMedia->getFileName() == $name) {
                    return true;
                }
            }
        }

        return false;
    }

    function getName()
    {
        return 'ROOT';
    }
}