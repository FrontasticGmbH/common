<?php

namespace Frontastic\Common\CoreBundle\Domain;

use Kore\DataObject\DataObject;

abstract class BaseObject extends DataObject
{
    /**
     * Creates a new instance of the class called on
     */
    public static function newWithProjectSpecificData(array $values): self
    {
        // @phpstan-ignore-next-line
        $dataObject = new static($values, false);
        $dataObject->rawApiInput = [];
        //@TODO: un-comment the following line after refactor dangerousInner* to a common name
        //$dataObject->rawApiOutput = null;

        return $dataObject;
    }

    /**
     * Raw api data from client to backend.
     *
     * This property should not be filled by Frontastic itself, but if can be done only to allow backward compatibility.
     * When present on write access to an API implementation MAY store information in appropriate way. It's up to the
     * client to provide the right format.
     *
     * In a project it can be used to carry custom input from frontend to backend in the following use case:
     *
     * - Transfer raw api data to the backend: Use an API Lifecycle Decorator to map data from $projectSpecificData into
     * $rawApiInput to make the API implementation carry it to the configured backend service, if the API supports it.
     *
     * This property should contain an object (\stdClass or data object) or an array (list).
     *
     * @var object|array
     */
    public $rawApiInput = [];

    /**
     * Access backend data from and to frontend.
     *
     * This should only be used for customization on a project basis. Depending on the option of the access we can find
     * the following use cases:
     *
     * - On write access: the data stored in $projectSpecificData can be used on an API Lifecycle Decorator to map
     * data from $projectSpecificData into $rawApiInput.
     *
     * - On read access: the client can use a API Lifecycle Decorator to map to map data from $dangerousInner*
     *  into $projectSpecificData (since $dangerousInner* will be stripped before sending).
     *
     * @var mixed
     */
    public $projectSpecificData = [];
}
