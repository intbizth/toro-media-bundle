<?php

namespace Toro\Bundle\MediaBundle\Form\Type;

use Doctrine\Common\Util\ClassUtils;
use Sylius\Bundle\ResourceBundle\Form\Type\ResourceTranslationsType;
use Symfony\Cmf\Bundle\MediaBundle\Form\Type\ImageType as CmfImageType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\UrlType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ImageCollectionType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('image', CmfImageType::class, [
                'label' => 'Image',
                'required' => false,
            ])
            ->add('filter', ChoiceType::class, [
                'label' => 'Filter',
                'choices' => $options['filters'],
                'required' => false,
            ])
            ->add('position', IntegerType::class, [
                'label' => 'Ordering',
                'required' => false,
            ])
            ->add('link', UrlType::class, [
                'label' => 'Link',
                'required' => false,
            ])
            ->add('translations', ResourceTranslationsType::class, [
                'entry_type' => ImageCollectionTranslationType::class,
                'label' => 'Translations',
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
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'filters' => []
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'image_collection';
    }
}
