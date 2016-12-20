<?php

namespace Toro\Bundle\MediaBundle\Model;

use Sylius\Component\Resource\Model\ResourceInterface;
use Sylius\Component\Resource\Model\TimestampableInterface;
use Sylius\Component\Resource\Model\TranslatableInterface;
use Symfony\Cmf\Bundle\MediaBundle\ImageInterface;
use Toro\Bundle\MediaBundle\Model\MediaAwareInterface;

interface ImageCollectionInterface extends
    ResourceInterface,
    TimestampableInterface,
    MediaAwareInterface,
    ImageCollectionTranslationInterface,
    TranslatableInterface
{
    /**
     * @return int
     */
    public function getPosition();

    /**
     * @param int $position
     */
    public function setPosition($position);

    /**
     * @return ImageCollectionAwareInterface
     */
    public function getCollectionOwner();

    /**
     * @param ImageCollectionAwareInterface $collectionOwner
     */
    public function setCollectionOwner(ImageCollectionAwareInterface $collectionOwner);

    /**
     * @return ImageInterface
     */
    public function getImage();

    /**
     * @param null|ImageInterface $image
     */
    public function setImage(ImageInterface $image = null);

    /**
     * @return string
     */
    public function getFilter();

    /**
     * @param string $filter
     */
    public function setFilter($filter);

    /**
     * @return string
     */
    public static function getCollectionOwnerTargetEntity();

    /**
     * @return string
     */
    public function getLink();

    /**
     * @param string $link
     */
    public function setLink($link);
}
