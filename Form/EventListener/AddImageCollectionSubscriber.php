<?php

namespace Toro\Bundle\MediaBundle\Form\EventListener;

use Doctrine\Common\Collections\Collection;
use Sylius\Component\Resource\Factory\FactoryInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\Util\StringUtil;
use Toro\Bundle\MediaBundle\Form\ImageCollectionConfigureResolverInterface;

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

    public function __construct(FactoryInterface $factory, ImageCollectionConfigureResolverInterface $resolver, $column = 'images')
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
        $data = $event->getData();

        if (!empty($imageCollection['create_default']) && !empty($imageCollection['filters'])) {
            /** @var Collection $images */
            $images = $data->getImages();

            foreach ($imageCollection['filters'] as $filterKey => $filter) {
                if ($images->filter(function (PersonalImageInterface $image) use ($filterKey) {
                    return $image->getFilter() === $filterKey;
                })->count()
                ) {
                    continue;
                }

                $image = $this->factory->createNew();
                $image->setFilter($filterKey);

                $data->addImage($image);
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
}
