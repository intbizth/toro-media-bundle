<?php

namespace Toro\Bundle\MediaBundle\Meta;

use Symfony\Cmf\Bundle\MediaBundle\FileInterface;
use Symfony\Cmf\Bundle\MediaBundle\ImageInterface;

class MediaReference
{
    /**
     * @var string
     */
    public $refName;

    /**
     * @var string
     */
    public $refValue;

    /**
     * @var string
     */
    public $path;

    /**
     * @var FileInterface|ImageInterface
     */
    public $media;

    public function __construct($path, $refName, $refValue, $media = null)
    {
        $this->refName = $refName;
        $this->refValue = $refValue;
        $this->path = $path;
        $this->media = $media;
    }

    /**
     * @param ImageInterface|\Closure|null $image
     *
     * @return ImageInterface|null
     */
    public static function getImage($image)
    {
        return $image instanceof \Closure ? call_user_func($image) : $image;
    }
}
