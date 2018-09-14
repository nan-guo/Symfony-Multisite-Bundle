<?php

require_once __DIR__.'/AppKernel.php';

use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class MultisiteKernel extends AppKernel
{
    public function registerBundles()
    {
        $bundles = parent::registerBundles();

        if (in_array($this->getEnvironment(), array('dev', 'test'))) {
            
        }

        return $bundles;
    }

    public function registerContainerConfiguration(LoaderInterface $loader)
    {
        $loader->load(__DIR__.'/config/config_'.$this->getEnvironment().'.yml');
    }

    public function getLogDir()
    {
        global $instance;
        return dirname(__DIR__).'/var/logs/logs_'.$instance;
    }

    public function getCacheDir()
    {
        global $instance;
        return dirname(__DIR__).'/var/cache/'.$this->getEnvironment().'/'.$instance;
    }

    /**
     * The extension point similar to the Bundle::build() method.
     *
     * Use this method to register compiler passes and manipulate the container during the building process.
     */
    protected function build(ContainerBuilder $container)
    {
        global $currentSite;
        $container->getParameterBag()->add(['kernel.instance' => $currentSite]);
    }
}
