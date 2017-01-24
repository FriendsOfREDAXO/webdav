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

            if ($media && $media->getCategoryId() == $category->getId()) {
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
     * @param string $path
     *
     * @return null|rex_media_category
     */
    public static function categoryForPath($path)
    {
        $parts = explode('/', ltrim($path, '/'));

        return self::resolvePath(rex_media_category::getRootCategories(), $parts);
    }

    /**
     * @param rex_media_category[] $categories
     * @param string[] $parts
     * @return null|rex_media_category
     */
    private static function resolvePath(array $categories, array $parts) {
        $kidPart = array_shift($parts);

        foreach($categories as $kid) {
            if ($kidPart == $kid->getName()) {
                if (0 === count($parts)) {
                    return $kid;
                }

                return self::resolvePath($kid->getChildren(), $parts);
            }
        }

        return null;
    }
}