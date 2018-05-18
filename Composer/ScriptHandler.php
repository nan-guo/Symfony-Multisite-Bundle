<?php

/**
 * @license MIT, http://opensource.org/licenses/MIT
 * @package symfony
 */

namespace Prodigious\MultisiteBundle\Composer;

use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Process\Process;
use Symfony\Component\Process\PhpExecutableFinder;
use Composer\Script\Event;
use Prodigious\MultisiteBundle\Util\FileLib;

/**
 * Performs bundle setup during composer installs
 *
 * @package symfony
 */
class ScriptHandler
{
	/**
	 * Installs the prodigious multisite bundle.
	 *
	 * @param Event $event Event instance
	 * @throws \RuntimeException If an error occured
	 */
	public static function installBundle( Event $event )
	{
		$options = self::getOptions( $event );

		$rootDir = getcwd();

		if( !isset( $options['symfony-app-dir'] ) || !is_dir( $options['symfony-app-dir'] ) ) {
			$msg = 'An error occurred because the "%1$s" option or the "%2$s" directory isn\'t available';
			throw new \RuntimeException( sprintf( $msg, 'symfony-app-dir', $options['symfony-app-dir'] ) );
		}

		if(!@file_exists($rootDir.'/sites')) {
			FileLib::sync(__DIR__.'/../Template/sites-dist', $rootDir.'/sites');
		}

		if(!@file_exists($options['symfony-app-dir'].'/MultisiteKernel.php')) {
			FileLib::sync(__DIR__.'/../Template/kernel-dist', $options['symfony-app-dir']);
		}
	}

	/**
	 * Returns the available options defined in the composer file.
	 *
	 * @param Event $event Command event object
	 * @return array Associative list of option keys and values
	 */
	protected static function getOptions( Event $event )
	{
		return $event->getComposer()->getPackage()->getExtra();
	}
}