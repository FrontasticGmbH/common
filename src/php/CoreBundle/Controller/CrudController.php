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
            ($body = json_decode($request->getContent(), true)) === null) {
            throw new \InvalidArgumentException("Invalid data passed: " . $request->getContent());
        }

        return $body;
    }

    protected function fillFromRequest(DataObject $entity, Request $request): DataObject
    {
        $body = $this->getJsonContent($request);

        return $this->fillFromArray($entity, $body);
    }

    protected function fillFromArray(DataObject $entity, array $body): DataObject
    {
        $versioner = new Versioner();

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

    protected function parseProjectSpecificDataByKeys(array $requestBody, array $keys): array
    {
        $projectSpecificData = $requestBody['projectSpecificData'] ?? [];

        foreach ($keys as $key) {
            if (!key_exists($key, $projectSpecificData) && key_exists($key, $requestBody)) {
                $this->get('logger')
                    ->warning(
                        'This usage of the key "{key}" is deprecated, move it into "projectSpecificData" instead',
                        ['key' => $key]
                    );
                $projectSpecificData[$key] = $requestBody[$key] ?? [];
            }
        }

        return $projectSpecificData;
    }
}
