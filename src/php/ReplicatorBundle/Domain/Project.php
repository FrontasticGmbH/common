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
     * @required
     */
    public $projectId;

    /**
     * @var string
     * @required
     */
    public $name;

    /**
     * @var string
     * @required
     */
    public $customer;

    /**
     * In the config this is the `secret`.
     *
     * @var string
     * @required
     */
    public $apiKey;

    /**
     * @var string
     */
    public $previewUrl;

    /**
     * @var object
     */
    public $preview;

    /**
     * @var string
     * @required
     */
    public $publicUrl;

    /**
     * @var int
     * @required
     */
    public $webpackPort;

    /**
     * @var int
     * @required
     */
    public $ssrPort;

    /**
     * @var string|null
     */
    public $encryptedFieldsPublicKey = null;

    /**
     * @var array
     * @required
     */
    public $configuration = [];

    /**
     * Additional external project data from sources like tideways. Does not
     * follow any defined schema.
     *
     * @var array
     * @required
     */
    public $data = [];

    /**
     * @var string[]
     * @required
     */
    public $languages = [];

    /**
     * @var string
     * @required
     */
    public $defaultLanguage;

    /**
     * @var string[]
     * @required
     */
    public $projectSpecific = [];

    /**
     * @var Endpoint[]
     * @required
     */
    public $endpoints = [];

    public function getConfigurationSection(string ...$sectionNamePath): object
    {
        $config = $this->configuration;
        $currentSectionNamePath = [];

        foreach ($sectionNamePath as $sectionName) {
            $currentSectionNamePath[] = $sectionName;

            $config = (array)$config;
            if (!array_key_exists($sectionName, $config)) {
                return new \stdClass();
            }

            $config = $config[$sectionName];
            if (is_array($config)) {
                $config = (object)$config;
            }

            if (!is_object($config)) {
                throw new \RuntimeException(
                    'Invalid project configuration section ' . implode('.', $currentSectionNamePath)
                );
            }
        }

        return $config;
    }

    public function getExtensionRunnerManagerUrl(string $environment): string
    {
        $multitenantConfiguration = (array) ($this->configuration['multitenant'] ?? []);
        $environments = $multitenantConfiguration['environments'] ?? [];
        if (!isset($environments[$environment])) {
            throw new \OutOfBoundsException('Extension Runner Manager configuration was not found.');
        }

        if ($environments[$environment] === 'http://localhost:8087') {
            return $environments[$environment];
        }

        return 'https://multitenant-gke-' . $environments[$environment] . '-extensions.frontastic.cloud';
    }
}
