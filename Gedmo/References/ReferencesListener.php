<?php

namespace Toro\Bundle\MediaBundle\Gedmo\References;

use Gedmo\References\ReferencesListener as BaseReferencesListener;
use Symfony\Component\DependencyInjection\ContainerInterface;

// Fix: Circular reference detected for service
class ReferencesListener extends BaseReferencesListener
{
    /**
     * @var ContainerInterface
     */
    private $container;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;

        parent::__construct([]);
    }

    public function getManager($type)
    {
        return 'entity' === strtolower($type)
            ? $this->container->get('doctrine.orm.entity_manager')
            : $this->container->get('doctrine_phpcr.odm.document_manager')
        ;
    }
}
