<?php

use Sabre\DAV;

class MediapoolDirectory extends DAV\Collection implements DAV\IMoveTarget
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

    function getLastModified()
    {
        $category = $this->categoryForPath($this->myPath);
        return $category->getUpdateDate();
    }

    function getName()
    {
        $category = $this->categoryForPath($this->myPath);

        return $category->getName();
    }

    /**
     * Renames the node
     *
     * @param string $name The new name
     * @return void
     */
    function setName($name) {
        $category = $this->categoryForPath($this->myPath);

        $db = rex_sql::factory();
        $db->setTable(rex::getTablePrefix() . 'media_category');
        $db->setValue('name', $name);
        $db->setWhere(['id' => $category->getId()]);
        $db->addGlobalUpdateFields();

        $db->update();

        rex_media_cache::deleteCategory($category->getId());
    }

    /**
     * Creates a new subdirectory
     *
     * @param string $name
     * @return void
     */
    function createDirectory($name) {
        $category = $this->categoryForPath($this->myPath);

        $db = rex_sql::factory();
        $db->setTable(rex::getTablePrefix() . 'media_category');
        $db->setValue('name', $name);
        $db->setValue('parent_id', $category->getId());
        $db->setValue('path', $category->getPath() . $category->getId().'|');
        $db->addGlobalCreateFields();
        $db->addGlobalUpdateFields();

        $db->insert();

        rex_media_cache::deleteCategoryList($category->getId());
    }
    
    function moveInto($targetName, $sourcePath, DAV\INode $sourceNode) {
        if ($sourceNode instanceof self) {
            $sourceCategory = self::categoryForPath($sourceNode->myPath);
            $targetCategory = self::categoryForPath($this->myPath);

            $db = rex_sql::factory();
            $db->setTable(rex::getTablePrefix() . 'media_category');
            $db->setValue('parent_id', $targetCategory->getId());
            $db->setValue('path', $targetCategory->getPath() . $targetCategory->getId().'|');
            $db->setWhere(['id' => $sourceCategory->getId()]);
            $db->addGlobalUpdateFields();

            $db->update();

            rex_media_cache::deleteCategory($sourceCategory->getId());
            rex_media_cache::deleteCategory($targetCategory->getId());

            return true;
        } else if ($sourceNode instanceof MediapoolFile) {
            $media = $sourceNode->getMedia();
            $targetCategory = self::categoryForPath($this->myPath);

            $db = rex_sql::factory();
            $db->setTable(rex::getTablePrefix() . 'media');
            $db->setValue('category_id', $targetCategory->getId());
            $db->setWhere(['id' => $media->getId()]);
            $db->addGlobalUpdateFields();

            $db->update();

            rex_media_cache::delete($media->getFileName());
            rex_media_cache::deleteCategory($targetCategory->getId());

            return true;
        }
        return false;
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