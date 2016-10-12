<?php

namespace Toro\Bundle\MediaBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class UploadImageController extends Controller
{
    public function dropzoneAction(Request $request, $id, $_resource, $_form, $_field)
    {
        $repository = $this->get('toro.repository.' . $_resource);
        $manager = $this->get('toro.manager.' . $_resource);

        $resource = $repository->find($id);
        $form = $this->get('form.factory')->createNamed('', $_form, $resource, ['csrf_protection' => false]);
        $data = [$_field => $request->files->get($_field)];

        if ($form->submit($data, false)->isValid()) {
            $manager->flush();
            return new JsonResponse('OK');
        }

        // TODO: error handing
        return new JsonResponse();
    }
}
