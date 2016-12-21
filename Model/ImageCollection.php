<?php

namespace Toro\Bundle\MediaBundle\Model;

use Sylius\Component\Resource\Model\TimestampableTrait;
use Sylius\Component\Resource\Model\TranslatableTrait;
use Toro\Bundle\MediaBundle\Meta\MediaReference;
use Symfony\Cmf\Bundle\MediaBundle\ImageInterface;

abstract class ImageCollection implements ImageCollectionInterface
{
    use TimestampableTrait;
    use TranslatableTrait {
        __construct as protected initializeTranslationsCollection;
    }

    /**
     * @var int
     */
    protected $id;

    /**
     * @var string
     */
    protected $filter;

    /**
     * @var int
     */
    protected $position = 0;

    /**
     * @var ImageCollectionAwareInterface
     */
    protected $collectionOwner;

    /**
     * @var ImageInterface
     */
    protected $image;

    /**
     * @var int
     */
    protected $imageId;

    /**
     * @var string
     */
    protected $link;

    public function __construct()
    {
        $this->initializeTranslationsCollection();
    }

    /**
     * {@inheritdoc}
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * {@inheritdoc}
     */
    public function getFilter()
    {
        return $this->filter;
    }

    /**
     * {@inheritdoc}
     */
    public function setCaption($caption)
    {
        $this->getTranslation()->setCaption($caption);
    }

    /**
     * {@inheritdoc}
     */
    public function getCaption()
    {
        return $this->getTranslation()->getCaption();
    }

    /**
     * {@inheritdoc}
     */
    public function setFilter($filter)
    {
        $this->filter = $filter;
    }

    /**
     * {@inheritdoc}
     */
    public function getPosition()
    {
        return $this->position;
    }

    /**
     * {@inheritdoc}
     */
    public function setPosition($position)
    {
        $this->position = $position;
    }

    /**
     * {@inheritdoc}
     */
    public function getCollectionOwner()
    {
        return $this->collectionOwner;
    }

    /**
     * {@inheritdoc}
     */
    public function setCollectionOwner(ImageCollectionAwareInterface $collectionOwner)
    {
        $this->collectionOwner = $collectionOwner;
    }

    /**
     * {@inheritdoc}
     */
    public function getImage()
    {
        return $this->image;
    }

    /**
     * {@inheritdoc}
     */
    public function setImage(ImageInterface $image = null)
    {
        $this->image = $image;

        // `image` no mapped for doctrine
        // we need to trig some field for doctrine changed tracker
        $this->updatedAt = new \DateTime();
    }

    /**
     * @return string
     */
    public function getLink()
    {
        return $this->link;
    }

    /**
     * @param string $link
     */
    public function setLink($link)
    {
        $this->link = $link;
    }

    /**
     * {@inheritdoc}
     */
    public function getMediaMetaReferences()
    {
        return array(
            new MediaReference('/images', 'imageId', $this->imageId, $this->image),
        );
    }
}
