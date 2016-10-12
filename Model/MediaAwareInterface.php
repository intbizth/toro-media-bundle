<?php

namespace Toro\Bundle\MediaBundle\Model;

use Toro\Bundle\MediaBundle\Meta\MediaReference;

interface MediaAwareInterface
{
    /**
     * @return MediaReference[]
     */
    public function getMediaMetaReferences();
}
