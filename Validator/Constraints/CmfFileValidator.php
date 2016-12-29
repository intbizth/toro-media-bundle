<?php

namespace Toro\Bundle\MediaBundle\Validator\Constraints;

use Symfony\Cmf\Bundle\MediaBundle\FileInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\Constraints\FileValidator;

class CmfFileValidator extends FileValidator
{
    public function validate($value, Constraint $constraint)
    {
        if (!$value instanceof FileInterface) {
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
