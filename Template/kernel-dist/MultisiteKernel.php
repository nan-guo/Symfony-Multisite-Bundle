<?php

require_once __DIR__.'/AppKernel.php';

use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpFoundation\Request;

class MultisiteKernel extends AppKernel
{
    private $site;

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
        return dirname(__DIR__).'/var/logs/logs_'.$this->site['instance'];
    }

    public function getCacheDir()
    {
        return dirname(__DIR__).'/var/cache/'.$this->getEnvironment().'/'.$this->site['instance'];
    }

    /**
     * The extension point similar to the Bundle::build() method.
     *
     * Use this method to register compiler passes and manipulate the container during the building process.
     */
    protected function build(ContainerBuilder $container)
    {
        $container->getParameterBag()->add(['kernel.instance' => $this->site['current_site']]);
    }

    /**
     * {@inheritdoc}
     */
    public function handle(Request $request, $type = HttpKernelInterface::MASTER_REQUEST, $catch = true)
    {
        $request->attributes->set('instance', $this->site['instance']);
        $request->attributes->set('site', $this->site['current_site']);
        if(!empty($site['current_locale'])) {
            $request->setLocale($this->site['current_locale']);
        }
        return parent::handle($request, $type, $catch);
    }

    /**
     * @param array $site
     */
    public function setSite($site)
    {
        $this->site = $site;
    }

    /**
     * @return array
     */
    public function getSite()
    {
        return $this->site;
    }
}
