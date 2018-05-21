<?php

namespace Prodigious\MultisiteBundle\Manager;

use Symfony\Component\Yaml\Yaml;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Prodigious\MultisiteBundle\Util\FileLib;

class SiteManager
{
    /**
     * @var ContainerInterface
     */
    private $container;

    /**
     * @var string
     */
    private $configFile;

    /**
     * @var string
     */
    private $projectDir;

    /**
     * Constructor
     *
     * @param ContainerInterface $container
     */
    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
        $this->projectDir = $container->get('kernel')
            ->getProjectDir();
        $this->configFile = $this->projectDir . '/sites/sites.yml';
    }

    /**
     * Get all sites
     */
    public function list()
    {
        $configs = Yaml::parseFile($this->configFile);
        return $configs['sites'] ?? [];
    }

    /**
     * Get config file
     */
    public function getConfigFile()
    {
        $this->configFile;
    }

    /**
     * Find a site by name
     *
     * @param string $name
     */
    public function find($name)
    {
        $sites = $this->list();
        return $sites[$name] ?? [];
    }

    /**
     * Check host exist or not
     */
    public function checkHost($name, $host)
    {
        $instances = $this->find($name);

        foreach ($instances as $instance) {
            if ($host == $instance['host']) {
                return false;
            }
        }

        return true;
    }

    /**
     * Create a new site
     *
     * @param string $name
     */
    public function create($name, $host, $locale)
    {
        if (!@file_exists($this->projectDir . '/app_' . $name)) {
            // Create directory for site
            FileLib::sync($this->projectDir . '/app', $this->projectDir . '/app_' . $name);

            // Write configs to composer.json
            $this->addComposerExtraConfig($name);

            // Create public directory
            $this->createPublicDir($name);
        }

        // Create robots.txt
        $this->createRobotsTxt($host);
    }

    /**
     * Check if site exsit or not
     */
    public function exists($name)
    {
        return @file_exists($this->projectDir . '/app_' . $name);
    }

    /**
     * Add new config to composer.json
     *
     * @var string $name
     */
    public function addComposerExtraConfig($name)
    {
        $composer = json_decode(file_get_contents($this->projectDir . '/composer.json'));
        if (isset($composer->extra)) {
            $extra = get_object_vars($composer->extra);
            if (isset($extra['incenteev-parameters'])) {

                if (!is_array($extra['incenteev-parameters'])) {
                    $incenteevParameters[] = $extra['incenteev-parameters'];
                } else {
                    $incenteevParameters = $extra['incenteev-parameters'];
                }

                $newConfig = $this->projectDir . '/app_' . $name . '/config/parameters.yml';
                $item = NULL;
                foreach ($incenteevParameters as $obj) {
                    if ($newConfig == $obj->file) {
                        $item = $obj;
                        break;
                    }
                }

                if (is_null($item)) {
                    $newParameters = new \stdClass();
                    $newParameters->file = $newConfig;
                    $incenteevParameters[] = $newParameters;
                    $extra['incenteev-parameters'] = $incenteevParameters;
                    $composer->extra = $extra;
                    FileLib::dumpFile($this->projectDir . '/composer.json', json_encode($composer, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT));
                }
            }
        }
    }

    /**
     * Remove config to composer.json
     *
     * @var string $name
     */
    public function removeComposerExtraConfig($name)
    {
        $composer = json_decode(file_get_contents($this->projectDir . '/composer.json'));
        $config = $this->projectDir . '/app_' . $name . '/config/parameters.yml';

        if (isset($composer->extra)) {
            $extra = get_object_vars($composer->extra);
            if (!is_array($extra['incenteev-parameters'])) {
                $incenteevParameters[] = $extra['incenteev-parameters'];
            } else {
                $incenteevParameters = $extra['incenteev-parameters'];
            }

            foreach ($incenteevParameters as $key => $obj) {
                if (isset($obj->file) && $config == $obj->file) {
                    unset($incenteevParameters[$key]);
                }
            }

            $extra['incenteev-parameters'] = $incenteevParameters;

            $composer->extra = $extra;

            FileLib::dumpFile($this->projectDir . '/composer.json', json_encode($composer, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT));
        }
    }

    /**
     * Create a file robots.txt
     *
     * @param string $host
     */
    public function createRobotsTxt($host)
    {
        FileLib::copy($this->projectDir . '/web/robots.txt', $this->projectDir . '/web/robots/' . $host . '.txt', true);
    }

    /**
     * Create a public directory
     *
     * @param string $host
     */
    public function createPublicDir($name)
    {
        FileLib::mkdir($this->projectDir . '/web/public/' . $name);
    }

    /**
     * Update parameters.yml
     *
     * @param string $host
     */
    public function updateParameters($name)
    {
        FileLib::remove($this->projectDir . '/app_' . $name . '/config/parameters.yml');
        exec('composer install');
    }

    /**
     * Update parameters.yml
     *
     * @param array $sites
     */
    public function updateSiteConfigs($sites)
    {
        $yaml = Yaml::dump(['sites' => $sites]);

        file_put_contents($this->configFile, $yaml);
    }

    /**
     * Remove site app directory
     *
     * @param string $name
     */
    public function deleteAppDirectory($name)
    {
        if (!empty($name)) {
            FileLib::remove($this->projectDir . '/app_' . $name);
        }
    }

    /**
     * Remove site public directory
     *
     * @param string $name
     */
    public function deletePublicDirectory($name)
    {
        if (!empty($name)) {
            FileLib::remove($this->projectDir . '/web/public/' . $name);
        }
    }

    /**
     * Remove site robots.txt
     *
     * @param string $host
     */
    public function deleteRobotsFile($host)
    {
        if (!empty($host)) {
            FileLib::remove($this->projectDir . '/web/robots/' . $host . '.txt');
        }
    }
}