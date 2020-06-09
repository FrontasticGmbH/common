<?php

namespace Frontastic\Common\ReplicatorBundle\Domain;

use Kore\DataObject\DataObject;

/**
 * @type
 */
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
     * In the config this is the `secret`.
     *
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
     * @var int
     */
    public $ssrPort;

    /**
     * @var array
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
     * @var string
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

    public function getConfigurationSection(string $sectionName): object
    {
        if (!array_key_exists($sectionName, $this->configuration)) {
            return new \stdClass();
        }

        $config = $this->configuration[$sectionName];
        if (is_array($config)) {
            $config = (object)$config;
        }

        if (!is_object($config)) {
            throw new \RuntimeException('Invalid project configuration section ' . $sectionName);
        }
        return $config;
    }
}
