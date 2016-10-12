<?php

namespace Toro\Bundle\MediaBundle\Form\Type;

use Symfony\Cmf\Bundle\MediaBundle\Form\Type\FileType as BaseFileType;
use Symfony\Component\Form\FormBuilderInterface;
use Toro\Bundle\MediaBundle\Form\EventListener\AddRemoveFileFieldSubscriber;

class FileType extends BaseFileType
{
    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'toro_media_file';
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
