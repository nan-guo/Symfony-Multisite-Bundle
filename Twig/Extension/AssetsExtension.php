<?php

namespace Prodigious\MultisiteBundle\Twig\Extension;

use Symfony\Component\Asset\Packages;
use Symfony\Component\HttpFoundation\RequestStack;

class AssetsExtension extends \Symfony\Bridge\Twig\Extension\AssetExtension
{
    /**
     * @var RequestStack
     */
	private $requestStack;

	/**
     * @var Packages
     */
	private $packages;

	public function __construct(Packages $packages, RequestStack $requestStack)
    {
        $this->packages = $packages;
        $this->requestStack = $requestStack;
    }

	/*
	 * {@inheritdoc}
	 */
	public function getAssetUrl($path, $mutilsite = true, $packageName = null)
    {
    	if($mutilsite) {
    		$request = $this->requestStack->getCurrentRequest();
    		$site = $request->attributes->get('site');
    		if($site != 'app')
    			return $this->packages->getUrl('/public/'.$site.$path, $packageName);	
    	}
    	
    	return $this->packages->getUrl($path, $packageName);	        
    }

    /**
     * Returns the name of the extension.
     *
     * @return string The extension name
     */
    public function getName()
    {
        return 'multisite_asset';
    }
}