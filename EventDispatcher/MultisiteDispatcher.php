<?php

namespace Prodigious\MultisiteBundle\EventDispatcher;

use Symfony\Component\Yaml\Yaml;

class MultisiteDispatcher
{
	private $sites;

	private $instance;

	private $currentLocale;

	private $currentSite;

	private $rootDir;

	public function __construct($rootDir)
	{
		$this->instance = 'app';

		$this->currentSite = 'app';

		$this->currentLocale = null;

		$this->rootDir = $rootDir;

		$this->sites = Yaml::parseFile($this->rootDir.'/sites/sites.yml');;
	}

	public function run($site = null)
	{
		if(is_null($site))
			$site = $_SERVER['SERVER_NAME'];

		if(!empty($this->sites['sites'])) {
			foreach ($this->sites['sites'] as $folder => $hosts) {
				foreach ($hosts as $host) {
					if($host['active'] === true && $host['host'] == $site) {
						$this->instance = 'app_'.$folder;
						$this->currentLocale = $host['locale'];
						$this->currentSite = $folder;
					}
				}
			}
		}

		$this->setGlobalVariables();

		$this->autoload();

		return $this->instance;
	}

	public function console($site)
	{
		if(!empty($this->sites['sites']) && isset($this->sites['sites'][$site])) {
			$this->instance = 'app_'.$site;
		}

		$this->setGlobalVariables();

		$this->autoload();

		return $this->instance;
	}

	public function setGlobalVariables()
	{
		$GLOBALS['instance'] = $this->instance;
		$GLOBALS['currentLocale'] = $this->currentLocale;
		$GLOBALS['currentSite'] = $this->currentSite;
	}

	public function autoload()
	{
		require_once $this->rootDir.'/'.$this->instance.'/MultisiteKernel.php';
	}

}