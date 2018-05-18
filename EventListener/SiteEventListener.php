<?php

namespace Prodigious\MultisiteBundle\EventListener;

use Symfony\Component\Console\Event\ConsoleCommandEvent;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\ArgvInput;

class SiteEventListener
{
	public function onConsoleCommand(ConsoleCommandEvent $event)
	{
		$definition = $event->getCommand()->getDefinition();

		$input = $event->getInput();

		$option = new InputOption('site', null, InputOption::VALUE_OPTIONAL, 'The site name', null);

		$definition->addOption($option);

		$input->bind($definition);

		$definition = $event->getCommand()->getApplication()->getDefinition();

		$definition->addOption($option);

		return $event;
	}
}