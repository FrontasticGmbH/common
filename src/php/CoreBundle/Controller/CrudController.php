<?php

namespace Frontastic\Common\CoreBundle\Controller;

use Frontastic\Common\CoreBundle\Domain\Versioner;
use Kore\DataObject\DataObject;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

abstract class CrudController extends Controller
{
    /**
     * @param Request $request
     * @return array|mixed
     */
    protected function getJsonContent(Request $request)
    {
        if (!$request->getContent() ||
            !($body = json_decode($request->getContent(), true))) {
            throw new \InvalidArgumentException("Invalid data passed: " . $request->getContent());
        }

        return $body;
    }

    protected function fillFromRequest(DataObject $entity, Request $request): DataObject
    {
        $versioner = new Versioner();

        $body = $this->getJsonContent($request);

        if ($versioner->supports($entity)) {
            $versioner->versionSnapshot($entity);
        }

        foreach ($body as $property => $value) {
            if (in_array($property, ['versions', 'metaData', '_type'])) {
                continue;
            }

            $entity->$property = $value;
        }

        return $entity;
    }
}
