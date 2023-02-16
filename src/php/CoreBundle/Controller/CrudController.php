<?php

namespace Frontastic\Common\CoreBundle\Controller;

use Frontastic\Common\CoreBundle\Domain\Json\Json;
use Frontastic\Common\CoreBundle\Domain\Versioner;
use Kore\DataObject\DataObject;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;

abstract class CrudController extends AbstractController
{
    /**
     * @param Request $request
     * @return array|mixed
     */
    protected function getJsonContent(Request $request)
    {
        if (!$request->getContent() ||
            ($body = Json::decode($request->getContent(), true)) === null) {
            throw new \InvalidArgumentException("Invalid data passed: " . $request->getContent());
        }

        return $body;
    }

    protected function fillFromRequest(
        DataObject $entity,
        Request $request,
        bool $ignoreUndefinedProperties = false
    ): DataObject {
        $body = $this->getJsonContent($request);

        return $this->fillFromArray($entity, $body, $ignoreUndefinedProperties);
    }

    protected function fillFromArray(
        DataObject $entity,
        array $body,
        bool $ignoreUndefinedProperties = false
    ): DataObject {
        $versioner = new Versioner();

        if ($versioner->supports($entity)) {
            $versioner->versionSnapshot($entity);
        }

        foreach ($body as $property => $value) {
            if (in_array($property, ['versions', 'metaData', '_type'])) {
                continue;
            }

            if ($ignoreUndefinedProperties && !property_exists($entity, $property)) {
                continue;
            }
            
            $entity->$property = $value;
        }

        return $entity;
    }
}
