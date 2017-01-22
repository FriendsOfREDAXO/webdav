<?php

use Sabre\DAV;

class MediapoolDirectory extends DAV\Collection
{
    private $myPath;

    function __construct($myPath)
    {
        $this->myPath = $myPath;
    }

    function getChildren()
    {
        $children = array();
        $category = $this->categoryForPath($this->myPath);

        foreach ($category->getChildren() as $childCategory) {
            $path = $this->myPath . '/' . $childCategory->getName();
            $children[] = new self($path);
        }

        foreach ($category->getMedia() as $media) {
            $path = $this->myPath . '/' . $media->getFileName();
            $children[] = new MediapoolFile($path);
        }

        return $children;
    }

    function getChild($name)
    {
        $category = $this->categoryForPath($this->myPath);

        if ($category) {
            $media = rex_media::get($name);
            $path = $this->myPath . '/' . $name;

            if ($media->getCategoryId() == $category->getId()) {
                return new MediapoolFile($path);
            }

            foreach ($category->getChildren() as $childCategory) {
                if ($childCategory->getName() == $name) {
                    return new self($path);
                }
            }
        }

        throw new DAV\Exception\NotFound('The file with name: '. $this->myPath.'/'.$name . ' could not be found');
    }

    function childExists($name)
    {
        $category = $this->categoryForPath($this->myPath . '/' . $name);

        return $category != null;
    }

    function getName()
    {
        $category = $this->categoryForPath($this->myPath);

        return $category->getName();
    }

    /**
     * @param $path
     *
     * @return null|rex_media_category
     */
    public static function categoryForPath($path)
    {
        $parts = explode('/', ltrim($path, '/'));
        $rootPart = array_shift($parts);
        foreach(rex_media_category::getRootCategories() as $rootCategory) {
            if ($rootCategory->getName() == $rootPart) {
                if (0 === count($parts)) {
                    return $rootCategory;
                }

                $kids = $rootCategory->getChildren();
                $kidPart = array_shift($parts);

                foreach($kids as $kidCategory) {
                    if ($kidPart == $kidCategory->getName()) {
                        if (0 === count($parts)) {
                            return $kidCategory;
                        }
                        /// XXX recursive lookup for more then 2 levels
                    }
                }
                break;
            }
        }
        
        return null;
    }
}