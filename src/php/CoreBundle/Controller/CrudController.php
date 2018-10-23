<?php

namespace Frontastic\Common\CoreBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;

use Kore\DataObject\DataObject;

use Frontastic\UserBundle\Domain\MetaData;

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
        $body = $this->getJsonContent($request);
        if (property_exists($entity, 'versions')) {
            $oldEntity = clone $entity;
            $oldEntity->versions = [];

            $entity->versions = $entity->versions ?: [];
            array_unshift($entity->versions, $oldEntity);
            $entity->versions = array_slice($entity->versions, 0, 32);
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
