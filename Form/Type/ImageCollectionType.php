<?php

namespace Toro\Bundle\MediaBundle\Form\Type;

use Sylius\Bundle\ResourceBundle\Form\Type\AbstractResourceType;
use Sylius\Bundle\ResourceBundle\Form\Type\ResourceTranslationsType;
use Symfony\Cmf\Bundle\MediaBundle\Form\Type\ImageType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class ImageCollectionType extends AbstractResourceType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('translations', ResourceTranslationsType::class, [
                'entry_type' => ImageCollectionTranslationType::class,
                'label' => 'Translations',
                'required' => false,
            ])
            ->add('filter', ChoiceType::class, [
                'label' => 'Filter',
                'choices' => [],
                'required' => false,
            ])
            ->add('position', IntegerType::class, [
                'label' => 'Ordering',
                'required' => false,
            ])
            ->add('image', ImageType::class, [
                'required' => false,
            ])

            ->addEventListener(FormEvents::PRE_SUBMIT, function(FormEvent $event) {
                $data = $event->getData();
                $uploaded = $data['image'];

                if (empty($uploaded)) {
                    if ($previousData = $event->getForm()->getData()) {
                        $data['image'] = $previousData->getImage();
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
            });
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'image_collection';
    }
}
