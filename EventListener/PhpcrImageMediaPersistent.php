<?php

namespace Toro\Bundle\MediaBundle\EventListener;

use Doctrine\Common\EventSubscriber;
use Doctrine\Common\Persistence\Event\LifecycleEventArgs;
use Doctrine\Common\Util\ClassUtils;
use Doctrine\ODM\PHPCR\DocumentManagerInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Toro\Bundle\MediaBundle\Doctrine\ODM\Phpcr\DocumentManagerHelper;
use Toro\Bundle\MediaBundle\Model\MediaAwareInterface;

class PhpcrImageMediaPersistent implements EventSubscriber
{
    /**
     * @var ContainerInterface
     */
    private $container;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    /**
     * {@inheritdoc}
     */
    public function getSubscribedEvents()
    {
        return array(
            'prePersist',
            'preUpdate',
        );
    }

    /**
     * {@inheritdoc}
     */
    public function prePersist(LifecycleEventArgs $args)
    {
        $this->flushMedia($args);
    }

    /**
     * {@inheritdoc}
     */
    public function preUpdate(LifecycleEventArgs $args)
    {
        $this->flushMedia($args);
    }

    /**
     * @return DocumentManagerInterface
     */
    private function getManager()
    {
        return $this->container->get('doctrine_phpcr.odm.document_manager');
    }

    /**
     * @param LifecycleEventArgs $args
     */
    private function flushMedia(LifecycleEventArgs $args)
    {
        $entity = $args->getObject();

        if (!$entity instanceof MediaAwareInterface) {
            return;
        }

        $dm = $this->getManager();

        foreach ($entity->getMediaMetaReferences() as $meta) {
            $refId = null;

            if ($meta->media) {
                $rootId = $this->container
                    ->getParameter('cmf_media.persistence.phpcr.media_basepath')
                ;

                if ($meta->path) {
                    $path = DocumentManagerHelper::cleanPath($rootId, $meta->path);
                    $dirs = DocumentManagerHelper::mkdirs($dm, strtolower($path));
                    $parent = end($dirs);

                    $meta->media->setParent($parent);
                }

                $dm->persist($meta->media);
            } else {
                // reset reference id, Gedmo references no reset automatic
                $entityReflect = ClassUtils::newReflectionObject($entity);
                $refKeyReflect = $entityReflect->getProperty($meta->refName);
                $refKeyReflect->setAccessible(true);
                $refKeyReflect->setValue($entity, null);
            }

            if ($meta->refValue && $meta->refValue !== ($meta->media ? $meta->media->getId() : null)) {
                if ($olderImage = $dm->find(null, $meta->refValue)) {
                    // TODO: check linked reference before remove, who using it should have checker ref service
                    $dm->remove($olderImage);
                }
            }
        }

        $dm->flush();
    }
}
