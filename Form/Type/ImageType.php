<?php

namespace Toro\Bundle\MediaBundle\Form\Type;

use Symfony\Cmf\Bundle\MediaBundle\Form\Type\ImageType as BaseImageType;
use Symfony\Component\Form\FormBuilderInterface;
use Toro\Bundle\MediaBundle\Form\EventListener\AddRemoveFileFieldSubscriber;

class ImageType extends BaseImageType
{
    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'toro_media_image';
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        parent::buildForm($builder, $options);

        $builder->addEventSubscriber(new AddRemoveFileFieldSubscriber());
    }
}
