<?php

namespace Toro\Bundle\MediaBundle\Model;

use Doctrine\Common\Collections\Collection;

interface ImageCollectionAwareInterface
{
    /**
     * @return Collection|ImageCollectionInterface[]
     */
    public function getImageCollections();

    /**
     * @param ImageCollectionInterface $imageCollection
     *
     * @return bool
     */
    public function hasImageCollection(ImageCollectionInterface $imageCollection);

    /**
     * @param ImageCollectionInterface $imageCollection
     */
    public function addImageCollection(ImageCollectionInterface $imageCollection);

    /**
     * @param ImageCollectionInterface $imageCollection
     */
    public function removeImageCollection(ImageCollectionInterface $imageCollection);

    /**
     * @return string
     */
    public static function getImageCollectionTargetEntity();

    /**
     * @param string $code
     *
     * @return Collection|self[]
     */
    public function findByCode($code);

    /**
     * @param string $code
     *
     * @return self
     */
    public function findOneByCode($code);
}
