<?php

namespace Frontastic\Common;

use Frontastic\ContainerBuilder;
use Symfony\Component\HttpKernel\Kernel as SymfonyKernel;
use Symfony\Component\Config\Loader\LoaderInterface;

abstract class Kernel extends SymfonyKernel
{
    /**
     * Configuration
     *
     * @var array
     */
    protected static $configuration;

    abstract public static function getBaseDir(): string;

    protected static function getComponentName(): string
    {
        return basename(static::getBaseDir());
    }

    /**
     * Register symfony configuration from base dir.
     *
     * @TODO Use Symfony Flex mechanism instead
     */
    public function registerContainerConfiguration(LoaderInterface $loader)
    {
        if (in_array($this->getEnvironment(), array('dev', 'test'))) {
            $loader->load(static::getBaseDir() . '/config/config_' . $this->getEnvironment() . '.yml');
        } else {
            $loader->load(static::getBaseDir() . '/config/config.yml');
        }
    }

    /**
     * Symfony determines this be Kernel file location otherwise, this does not
     * work for Catwalks.
     */
    public function getRootDir()
    {
        return static::getBaseDir();
    }

    public function getCacheDir(): string
    {
        $configuration = static::getConfiguration();
        $cacheDir = $configuration['cache_dir'] . '/' . static::getComponentName() . '/' . $this->environment;

        if ('dev' === $configuration['env']) {
            return $cacheDir;
        }
        $version = null;
        if (isset($configuration['version']) && false === empty($configuration['version'])) {
            $version = $configuration['version'];
        }
        $versionFile = dirname($cacheDir) . '/version.lock';
        if (false === file_exists($versionFile) || $version !== trim(file_get_contents($versionFile))) {
            if (!is_dir($cacheDir = dirname($versionFile))) {
                mkdir($cacheDir, 0755, true);
            }
            file_put_contents($versionFile, $version);
        }
        if ($version) {
            $cacheDir .= '@' . $version;
        }
        return $cacheDir;
    }

    public function getLogDir(): string
    {
        return static::getConfiguration()['log_dir'] . '/' . static::getComponentName() . '/' . $this->environment;
    }

    /**
     * Builds the service container.
     *
     * @return ContainerBuilder The compiled service container
     *
     * @throws \RuntimeException
     */
    protected function buildContainer()
    {
        $container = parent::buildContainer();

        foreach (static::getConfiguration() as $key => $value) {
            $container->setParameter($key, $value);
        }

        return $container;
    }

    /**
     * Initialize configuration
     *
     * @return void
     * @todo This parse_ini_file() stuff is deprecated, we use dotenv now
     */
    public static function getConfiguration()
    {
        if (static::$configuration) {
            return static::$configuration;
        }

        static::$configuration = self::getBaseConfiguration();

        foreach (static::getAdditionalConfigFiles() as $file) {
            if (file_exists($file)) {
                static::$configuration = array_merge(static::$configuration, parse_ini_file($file));
            }
        }

        return static::$configuration;
    }

    public static function getBaseConfiguration()
    {
        return array(
            'env' => 'prod',
            'locale' => 'en',
            'secret' => 'secret',
            'mailer.transport' => 'sendmail',
            'debug' => false,
            'monolog_action_level' => 'error',
            'cache_dir' => static::getBaseDir() . '/var/cache',
            'log_dir' => static::getBaseDir() . '/var/log',
        );
    }

    /**
     * Get environment
     *
     * @return string
     */
    public static function getEnvironmentFromConfiguration()
    {
        return getenv('env');
    }

    /**
     * Get additional config files
     *
     * @return string[]
     */
    public static function getAdditionalConfigFiles()
    {
        $files = array(
            static::getRootDir() . '/../environment',
            static::getBaseDir() . '/environment',
            static::getBaseDir() . '/environment.local',
        );

        if (getenv('CONFIG')) {
            $files[] = getenv('CONFIG');
        }
        return $files;
    }

    /**
     * Get debug
     *
     * @return bool
     */
    public static function getDebug()
    {
        return self::isDebugEnvironment(static::getEnvironmentFromConfiguration());
    }

    public static function isDebugEnvironment($environment)
    {
        return in_array($environment, array('dev', 'test'));
    }

    /**
     * Remove container cache file if our custom configuration files changed
     *
     * @return void
     */
    protected function initializeContainer()
    {
        $class = $this->getContainerClass();
        $containerCacheFile = $this->getCacheDir() . '/' . $class . '.php';

        if ($this->debug && file_exists($containerCacheFile)) {
            foreach (static::getAdditionalConfigFiles() as $configFile) {
                if (file_exists($configFile) &&
                    (filemtime($configFile) > filemtime($containerCacheFile))) {
                    unlink($containerCacheFile);
                    break;
                }
            }
        }

        return parent::initializeContainer();
    }
}
