<?php

namespace Toro\Bundle\MediaBundle\Form\EventListener;

use Doctrine\Common\Util\ClassUtils;
use Symfony\Cmf\Bundle\MediaBundle\FileInterface;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\PropertyAccess\PropertyAccess;

class AddRemoveFileFieldSubscriber implements EventSubscriberInterface
{
    /**
     * @var FileInterface|null
     */
    private $preSubmitFile;

    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents()
    {
        // Tells the dispatcher that we want to listen on the form.pre_set_data
        // event and that the preSetData method should be called.
        return array(
            FormEvents::PRE_SET_DATA => 'preSetData',
            FormEvents::PRE_SUBMIT => 'preSubmit',
            FormEvents::SUBMIT => 'submit',
        );
    }

    /**
     * @param $name
     *
     * @return string
     */
    private static function removeFileFieldName($name)
    {
        return '_remove_file_' . $name;
    }

    /**
     * {@inheritdoc}
     */
    public function preSubmit(FormEvent $event)
    {
        $parent = $event->getForm()->getParent();
        $accessor = PropertyAccess::createPropertyAccessor();

        if (null === $data = $parent->getData()) {
            return;
        }

        $uploaded = $event->getData();

        if ($uploaded instanceof UploadedFile) {
            $reflect = ClassUtils::newReflectionObject($uploaded);
            $reflectProperty = $reflect->getProperty('originalName');
            $reflectProperty->setAccessible(true);
            $fileName = sprintf('sys-%s.%s', md5($uploaded->getClientOriginalName() . time()), $uploaded->getClientOriginalExtension());
            $reflectProperty->setValue($uploaded, $fileName);
        }

        $this->preSubmitFile = $accessor->getValue($data, $event->getForm()->getPropertyPath());
    }

    /**
     * {@inheritdoc}
     */
    public function submit(FormEvent $event)
    {
        // user select an file
        if (!empty($event->getData())) {
            // do nothing
            return;
        }

        $parent = $event->getForm()->getParent();
        $submittedData = (array) $parent->get('_submitted_data')->getData();
        $isRemoveFileField = self::removeFileFieldName($event->getForm()->getName());

        // no selected file and tick remove file
        // do nothing form will submit null data to model
        // older file will remove automatic by our media listener
        if (!empty($submittedData[$isRemoveFileField])) {
            return;
        }

        // no select file and user don't want to remove old file
        // set file back to model prevent validation fail
        if (empty($submittedData[$isRemoveFileField])) {
            $event->setData($this->preSubmitFile);
            
            return;
        }
    }

    /**
     * {@inheritdoc}
     */
    public function preSetData(FormEvent $event)
    {
        $parent = $event->getForm()->getParent();

        // During form creation setData() is called with null as an argument
        // by the FormBuilder constructor. We're only concerned with when
        // setData is called with an actual Entity object in it (whether new,
        // or fetched with Doctrine). This if statement let's us skip right
        // over the null condition.
        if (null === $data = $parent->getData()) {
            return;
        }

        $form = $event->getForm();
        $factory = $form->getConfig()->getFormFactory();
        $accessor = PropertyAccess::createPropertyAccessor();

        // check if the data object has an file
        if ($accessor->getValue($data, $form->getPropertyPath())) {
            // monitor: https://github.com/symfony/symfony/pull/11241
            // now using Toro/Bundle/MediaBundle/Form/Extension/RootSubmittedStoreExtension to solve
            $parent->add($factory->createNamed(self::removeFileFieldName($form->getName()), CheckboxType::class, null, array(
                // need first for get submitted data inside file child.
                //'position' => 'first',
                'auto_initialize' => false,
                'required' => false,
                'mapped' => false,
            )));
        }
    }
}
