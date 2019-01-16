<?php

namespace Frontastic\Common\ReplicatorBundle\Domain;

use Kore\DataObject\DataObject;

class Project extends DataObject
{
    /**
     * @var string
     */
    public $projectId;

    /**
     * @var string
     */
    public $name;

    /**
     * @var string
     */
    public $customer;

    /**
     * @var string
     */
    public $apiKey;

    /**
     * @var string
     */
    public $previewUrl;

    /**
     * @var string
     */
    public $publicUrl;

    /**
     * @var int
     */
    public $webpackPort;

    /**
     * @var array[]
     */
    public $configuration = [];

    /**
     * Additional external project data from sources like tideways. Does not
     * follow any defined schema.
     *
     * @var array
     */
    public $data = [];

    /**
     * @var string[]
     */
    public $languages = [];

    /**
     * @var string[]
     */
    public $defaultLanguage;

    /**
     * @var string[]
     */
    public $projectSpecific = [];

    /**
     * @var Endpoint[]
     */
    public $endpoints = [];
}
