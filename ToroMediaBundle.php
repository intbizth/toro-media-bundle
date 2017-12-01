<?php

namespace Toro\Bundle\MediaBundle;

use Sylius\Bundle\ResourceBundle\AbstractResourceBundle;
use Sylius\Bundle\ResourceBundle\SyliusResourceBundle;

class ToroMediaBundle extends AbstractResourceBundle
{
    const APPLICATION_NAME = 'toro';

    /**
     * {@inheritdoc}
     */
    public function getSupportedDrivers(): array
    {
        return [
            SyliusResourceBundle::DRIVER_DOCTRINE_ORM,
        ];
    }

    /**
     * {@inheritdoc}
     */
    protected function getModelNamespace(): string
    {
        return 'Toro\Bundle\MediaBundle\Model';
    }
}
