<?php

namespace Toro\Bundle\MediaBundle\Model;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\Criteria;
use Doctrine\Common\Collections\ExpressionBuilder;

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

    /**
     * @param string $code
     *
     * @return ImageCollectionInterface
     */
    public function findOneByCode($code)
    {
        return $this->getImagesByCode($code)->first();
    }

    /**
     * @param string $code
     *
     * @return Collection|ImageCollectionInterface[]
     */
    public function findByCode($code)
    {
        $expr = new ExpressionBuilder();

        return $this->imageCollections->matching(new Criteria(
            $expr->eq('filter', $code)
        ));
    }
}
