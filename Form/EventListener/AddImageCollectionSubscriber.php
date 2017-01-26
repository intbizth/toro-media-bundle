<?php

namespace Toro\Bundle\MediaBundle\Form\EventListener;

use Sylius\Component\Resource\Factory\FactoryInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\Util\StringUtil;
use Toro\Bundle\MediaBundle\Form\ImageCollectionConfigureResolverInterface;
use Toro\Bundle\MediaBundle\Model\ImageCollectionInterface;

class AddImageCollectionSubscriber implements EventSubscriberInterface
{
    /**
     * @var string
     */
    private $column;

    /**
     * @var ImageCollectionConfigureResolverInterface
     */
    private $resolver;

    /**
     * @var FactoryInterface
     */
    private $factory;

    public function __construct(FactoryInterface $factory, ImageCollectionConfigureResolverInterface $resolver, $column = 'imageCollections')
    {
        $this->factory = $factory;
        $this->resolver = $resolver;
        $this->column = $column;
    }

    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents()
    {
        return array(
            FormEvents::PRE_SET_DATA => 'preSetData',
            FormEvents::POST_SET_DATA => 'postSetData',
            FormEvents::PRE_SUBMIT => 'preSubmit',
        );
    }

    /**
     * {@inheritdoc}
     */
    public function preSetData(FormEvent $event)
    {
        $column = $event->getForm()->get($this->column);
        $config = $column->getConfig();
        $imageCollectionKey = StringUtil::fqcnToBlockPrefix($config->getOption('entry_type'));
        $imageCollection = $this->resolver->getConfig($imageCollectionKey);
        /** @var ImageCollectionInterface $data */
        $data = $event->getData();

        if ($data && !empty($imageCollection['create_default']) && !empty($imageCollection['filters'])) {
            $images = $data->getImageCollections();

            foreach ($imageCollection['filters'] as $filterKey => $filter) {
                if ($images->filter(function (ImageCollectionInterface $image) use ($filterKey) {
                    return $image->getFilter() === $filterKey;
                })->count()
                ) {
                    continue;
                }

                $image = $this->factory->createNew();
                $image->setFilter($filterKey);

                $data->addImageCollection($image);
            }
        }
    }

    /**
     * {@inheritdoc}
     */
    public function postSetData(FormEvent $event)
    {
        $column = $event->getForm()->get($this->column);
        $config = $column->getConfig();
        $imageCollectionKey = StringUtil::fqcnToBlockPrefix($config->getOption('entry_type'));
        $imageCollection = $this->resolver->getConfig($imageCollectionKey);

        /** @var FormInterface $item */
        foreach ($column as $item) {
            $options = $item->get('filter')->getConfig()->getOptions();
            $item->remove('filter');
            $item->add('filter', ChoiceType::class, array_replace_recursive($options, [
                'choices' => array_flip($imageCollection['filters'])
            ]));
        }

        $prototype = $config->getAttribute('prototype');
        $filter = $prototype->get('filter');
        $options = $filter->getConfig()->getOptions();
        $prototype->remove('filter');
        $prototype->add('filter', ChoiceType::class, array_replace_recursive($options, [
            'choices' => array_flip($imageCollection['filters'])
        ]));
    }

    /**
     * {@inheritdoc}
     */
    public function preSubmit(FormEvent $event)
    {
        $column = $event->getForm()->get($this->column);
        $config = $column->getConfig();
        $options = $config->getOptions();
        $imageCollectionKey = StringUtil::fqcnToBlockPrefix($config->getOption('entry_type'));
        $imageCollection = $this->resolver->getConfig($imageCollectionKey);

        $event->getForm()->remove($this->column);
        $event->getForm()->add($this->column, CollectionType::class, array_replace_recursive($options, [
            'entry_options' => [
                'filters' => array_flip($imageCollection['filters'])
            ]
        ]));
    }
}
