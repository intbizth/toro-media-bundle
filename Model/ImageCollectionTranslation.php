<?php

namespace Toro\Bundle\MediaBundle\Model;

use Sylius\Component\Resource\Model\AbstractTranslation;
use Sylius\Component\Resource\Model\TranslationInterface;

abstract class ImageCollectionTranslation extends AbstractTranslation implements ImageCollectionTranslationInterface, TranslationInterface
{
    /**
     * @var int
     */
    protected $id;

    /**
     * @var string
     */
    protected $caption;

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
    public function getCaption()
    {
        return $this->caption;
    }

    /**
     * {@inheritdoc}
     */
    public function setCaption($caption)
    {
        $this->caption = $caption;
    }
}
