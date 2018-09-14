<?php

require_once __DIR__.'/AppKernel.php';

use Symfony\Component\Config\Loader\LoaderInterface;

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
     * Returns the kernel parameters.
     *
     * @return array An array of kernel parameters
     */
    protected function getKernelParameters()
    {
        global $currentSite;
        
        $params = parent::getKernelParameters();

        return array_merge($params, ['kernel.instance' => $currentSite]);
    }
}
