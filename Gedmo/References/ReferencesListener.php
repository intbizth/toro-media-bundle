<?php

namespace Toro\Bundle\MediaBundle\Gedmo\References;

use Doctrine\Common\EventArgs;
use Gedmo\References\ReferencesListener as BaseReferencesListener;
use Symfony\Component\DependencyInjection\ContainerInterface;

class ReferencesListener extends BaseReferencesListener
{
    /**
     * {@inheritdoc}
     */
    protected function getNamespace()
    {
        return __NAMESPACE__;
    }

    /**
     * {@inheritdoc}
     */
    public function postLoad(EventArgs $eventArgs)
    {
        $ea = $this->getEventAdapter($eventArgs);
        $om = $ea->getObjectManager();
        $object = $ea->getObject();
        $meta = $om->getClassMetadata(get_class($object));
        $config = $this->getConfiguration($om, $meta->name);

        if (isset($config['referenceOne'])) {
            foreach ($config['referenceOne'] as $mapping) {
                $property = $meta->reflClass->getProperty($mapping['field']);
                $property->setAccessible(true);
                if (isset($mapping['identifier'])) {
                    $referencedObjectId = $meta->getFieldValue($object, $mapping['identifier']);
                    if (null !== $referencedObjectId) {
                        if ($mapping['lazy']) {
                            if (null !== $referencedObjectId) {
                                $property->setValue($object, function () use ($ea, $mapping, $referencedObjectId) {
                                    return $ea->getSingleReference(
                                        $this->getManager($mapping['type']),
                                        $mapping['class'],
                                        $referencedObjectId
                                    );
                                });
                            }
                        } else {
                            $property->setValue(
                                $object,
                                $ea->getSingleReference(
                                    $this->getManager($mapping['type']),
                                    $mapping['class'],
                                    $referencedObjectId
                                )
                            );
                        }
                    }
                }
            }
        }

        if (isset($config['referenceMany'])) {
            foreach ($config['referenceMany'] as $mapping) {
                $property = $meta->reflClass->getProperty($mapping['field']);
                $property->setAccessible(true);
                if (isset($mapping['mappedBy'])) {
                    $id = $ea->extractIdentifier($om, $object);
                    $manager = $this->getManager($mapping['type']);
                    $class = $mapping['class'];
                    $refMeta = $manager->getClassMetadata($class);
                    $refConfig = $this->getConfiguration($manager, $refMeta->name);
                    if (isset($refConfig['referenceOne'][$mapping['mappedBy']])) {
                        $refMapping = $refConfig['referenceOne'][$mapping['mappedBy']];
                        $identifier = $refMapping['identifier'];
                        $property->setValue(
                            $object,
                            new LazyCollection(
                                function () use ($id, &$manager, $class, $identifier) {
                                    $results = $manager
                                        ->getRepository($class)
                                        ->findBy(array(
                                            $identifier => $id,
                                        ));

                                    return new ArrayCollection((is_array($results) ? $results : $results->toArray()));
                                }
                            )
                        );
                    }
                }
            }
        }

        $this->updateManyEmbedReferences($eventArgs);
    }
}
