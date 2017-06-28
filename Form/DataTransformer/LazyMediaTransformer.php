<?php

namespace Toro\Bundle\MediaBundle\Form\DataTransformer;

use Symfony\Component\Form\DataTransformerInterface;
use Toro\Bundle\MediaBundle\Meta\MediaReference;

class LazyMediaTransformer implements DataTransformerInterface
{
    /**
     * {@inheritdoc}
     */
    public function transform($value)
    {
        return MediaReference::getMedia($value);
    }

    /**
     * {@inheritdoc}
     */
    public function reverseTransform($value)
    {
        return $value;
    }
}
