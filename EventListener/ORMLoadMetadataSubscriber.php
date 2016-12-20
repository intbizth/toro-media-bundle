<?php

namespace Toro\Bundle\MediaBundle\EventListener;

use Doctrine\Common\EventSubscriber;
use Doctrine\Common\Persistence\Event\LoadClassMetadataEventArgs;
use Doctrine\Common\Persistence\Mapping\ClassMetadata;
use Doctrine\ORM\Events;
use Doctrine\ORM\Mapping\ClassMetadataInfo;
use Toro\Bundle\MediaBundle\Model\ImageCollectionAwareInterface;
use Toro\Bundle\MediaBundle\Model\ImageCollectionInterface;

class ORMLoadMetadataSubscriber implements EventSubscriber
{
    /**
     * {@inheritdoc}
     */
    public function getSubscribedEvents()
    {
        return [
            Events::loadClassMetadata,
        ];
    }

    /**
     * Add mapping to translatable entities
     *
     * @param LoadClassMetadataEventArgs $eventArgs
     */
    public function loadClassMetadata(LoadClassMetadataEventArgs $eventArgs)
    {
        $classMetadata = $eventArgs->getClassMetadata();
        $reflection = $classMetadata->reflClass;

        if (!$reflection || $reflection->isAbstract()) {
            return;
        }

        if ($reflection->implementsInterface(ImageCollectionAwareInterface::class)) {
            $this->mapImageCollectionAware($classMetadata);
        }

        if ($reflection->implementsInterface(ImageCollectionInterface::class)) {
            $this->mapImageCollection($classMetadata);
        }
    }

    /**
     * Add mapping data to a translatable entity.
     *
     * @param ClassMetadata $metadata
     */
    private function mapImageCollectionAware(ClassMetadata $metadata)
    {
        $className = $metadata->name;

        $metadata->mapOneToMany([
            'fieldName' => 'imageCollections',
            'targetEntity' => $className::getImageCollectionTargetEntity(),
            'mappedBy' => 'collectionOwner',
            'fetch' => ClassMetadataInfo::FETCH_EXTRA_LAZY,
            'cascade' => ['all'],
            'orphanRemoval' => true,
            'orderBy' => ['position' => 'asc'],
        ]);
    }

    /**
     * Add mapping data to a translation entity.
     *
     * @param ClassMetadata $metadata
     */
    private function mapImageCollection(ClassMetadata $metadata)
    {
        $className = $metadata->name;

        $metadata->mapManyToOne([
            'fieldName' => 'collectionOwner',
            'targetEntity' => $className::getCollectionOwnerTargetEntity(),
            'inversedBy' => 'imageCollections',
            'joinColumns' => [[
                'name' => 'owner_collection_id',
                'referencedColumnName' => 'id',
                'onDelete' => 'CASCADE',
                'nullable' => false,
            ]],
        ]);
    }
}
