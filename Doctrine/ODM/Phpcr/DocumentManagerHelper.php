<?php

namespace Toro\Bundle\MediaBundle\Doctrine\ODM\Phpcr;

use Doctrine\ODM\PHPCR\DocumentManagerInterface;
use Symfony\Cmf\Bundle\MediaBundle\Doctrine\Phpcr\Directory;

/**
 * @author dos <phaiboon@intbizth.com>
 */
class DocumentManagerHelper
{
    /**
     * Clean up paths.
     *
     * @return string
     */
    public static function cleanPath()
    {
        $paths = array();
        foreach (func_get_args() as $path) {
            $paths[] = $path;
        }

        return preg_replace('|//|', '/', join('/', $paths));
    }

    /**
     * @param $path
     *
     * @return array
     */
    public static function explodePath($path)
    {
        return explode('/', self::cleanPath($path));
    }

    /**
     * @param DocumentManagerInterface $dm
     * @param $path
     *
     * @return Directory[]
     */
    public static function mkdirs(DocumentManagerInterface $dm, $path)
    {
        $paths = self::explodePath($path);
        $parent = null;
        $dirs = array();

        foreach ($paths as $path) {
            if ($dir = $dm->find(null, self::cleanPath($parent, $path))) {
                $dirs[] = $dir;
            } else {
                $dirs[] = self::mkdir($dm, $parent, $path);
            }

            $parent = self::cleanPath($parent, $path);
        }

        return $dirs;
    }

    /**
     * @param DocumentManagerInterface $dm
     * @param $path
     * @param $name
     *
     * @return bool|Directory
     */
    public static function mkdir(DocumentManagerInterface $dm, $path, $name)
    {
        $dirname = self::cleanPath($path, $name);

        if ($dm->find(null, $dirname)) {
            return false;
        }

        $dir = new Directory();
        $dir->setName($name);
        $dir->setId($dirname);

        $dm->persist($dir);
        $dm->flush();

        return $dir;
    }
}
