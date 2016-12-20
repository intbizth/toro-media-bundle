<?php

namespace Toro\Bundle\MediaBundle\Model;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

trait ImageCollectionAwareTrait
{
    /**
     * @var Collection|ImageCollectionInterface[]
     */
    protected $imageCollections;

    public function __construct()
    {
        $this->imageCollections = new ArrayCollection();
    }

    /**
     * {@inheritdoc}
     */
    public function getImageCollections()
    {
        return $this->imageCollections;
    }

    /**
     * {@inheritdoc}
     */
    public function hasImageCollection(ImageCollectionInterface $imageCollection)
    {
        return $this->imageCollections->contains($imageCollection);
    }

    /**
     * {@inheritdoc}
     */
    public function addImageCollection(ImageCollectionInterface $imageCollection)
    {
        if (!$this->hasImageCollection($imageCollection)) {
            $imageCollection->setCollectionOwner($this);
            $this->imageCollections->add($imageCollection);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function removeImageCollection(ImageCollectionInterface $imageCollection)
    {
        if ($this->hasImageCollection($imageCollection)) {
            $this->imageCollections->removeElement($imageCollection);
        }
    }
}
