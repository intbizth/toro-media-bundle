<?php

namespace Toro\Bundle\MediaBundle\Form;

interface ImageCollectionConfigureResolverInterface
{
    /**
     * @return array
     */
    public function getConfigs();

    /**
     * @param string $name
     *
     * @return mixed
     */
    public function getConfig($name);
}
