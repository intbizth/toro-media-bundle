<?php

namespace Toro\Bundle\MediaBundle\Form\EventListener;

use Doctrine\Common\Util\ClassUtils;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class CmfMediaCollectionUploadSubscriber implements EventSubscriberInterface
{
    /**
     * @var string
     */
    private $imageField;

    public function __construct($imageField)
    {
        $this->imageField = $imageField;
    }

    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents()
    {
        return array(
            FormEvents::PRE_SUBMIT => 'preSubmit',
        );
    }

    /**
     * {@inheritdoc}
     */
    public function preSubmit(FormEvent $event)
    {
        $data = $event->getData();
        $uploaded = $data[$this->imageField];

        if (empty($uploaded)) {
            if ($previousData = $event->getForm()->getData()) {
                $reflect = ClassUtils::newReflectionObject($previousData);
                $reflectProperty = $reflect->getProperty($this->imageField);
                $reflectProperty->setAccessible(true);

                $data[$this->imageField] = $reflectProperty->getValue($previousData);
                $event->setData($data);
            }

            return;
        }

        if ($uploaded instanceof UploadedFile) {
            $reflect = ClassUtils::newReflectionObject($uploaded);
            $reflectProperty = $reflect->getProperty('originalName');
            $reflectProperty->setAccessible(true);
            $fileName = sprintf('sys-%s.%s', md5($uploaded->getClientOriginalName() . time()), $uploaded->getClientOriginalExtension());
            $reflectProperty->setValue($uploaded, $fileName);
        }
    }
}
