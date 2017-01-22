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

        return $children;
    }

    function getChild($name)
    {
        foreach (rex_media_category::getRootCategories() as $rootCategory) {
            if ($rootCategory->getName() == $name) {
                return new MediapoolDirectory('/'. $name);
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

        return false;
    }

    function getName()
    {
        return 'ROOT';
    }
}