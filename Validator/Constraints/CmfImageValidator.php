<?php

namespace Toro\Bundle\MediaBundle\Validator\Constraints;

use Symfony\Cmf\Bundle\MediaBundle\ImageInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\Constraints\ImageValidator;

class CmfImageValidator extends ImageValidator
{
    public function validate($value, Constraint $constraint)
    {
        if (!$value instanceof ImageInterface) {
            return;
        }

        // TODO: https://github.com/symfony-cmf/media-bundle/issues/97
        $file = sprintf('/tmp/' . $value->getName());
        file_put_contents($file, $value->getContentAsString());
        $value = new UploadedFile($file, $value->getName(), $value->getContentType(), $value->getSize(), false, true);

        try {
            parent::validate($value, $constraint);
        } catch (\Exception $e) {
            @unlink($file);
            throw  $e;
        }

        @unlink($file);
    }
}
