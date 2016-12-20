<?php

namespace Toro\Bundle\MediaBundle\Model;

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
}
