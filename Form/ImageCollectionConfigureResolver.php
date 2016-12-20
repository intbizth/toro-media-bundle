<?php

namespace Toro\Bundle\MediaBundle\Form;

class ImageCollectionConfigureResolver implements ImageCollectionConfigureResolverInterface
{
    private $configs = [];

    public function __construct(array $configs = [])
    {
        $this->configs = $configs;
    }

    /**
     * {@inheritdoc}
     */
    public function getConfigs()
    {
        return $this->configs;
    }

    /**
     * {@inheritdoc}
     */
    public function getConfig($name)
    {
        if (array_key_exists($name, $this->configs)) {
            return $this->configs[$name];
        }

        return;
    }
}
