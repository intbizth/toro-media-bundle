<?php

namespace Toro\Bundle\MediaBundle\Model;

use Sylius\Component\Resource\Model\ResourceInterface;
use Sylius\Component\Resource\Model\TranslationInterface;

interface ImageCollectionTranslationInterface extends ResourceInterface
{
    /**
     * @return string
     */
    public function getCaption();

    /**
     * @param string $caption
     */
    public function setCaption($caption);
}
