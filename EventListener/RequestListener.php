<?php

namespace Prodigious\MultisiteBundle\EventListener;

use Symfony\Component\HttpKernel\Event\GetResponseEvent;

class RequestListener
{
	/**
	 * @param GetResponseEvent $event
	 */
	public function onKernelResquest(GetResponseEvent $event)
	{
		if (!$event->isMasterRequest()) {
            return;
        }
        global $instance;
        global $currentLocale;
        global $currentSite;

		$request = $event->getRequest();
		$request->attributes->set('instance', $instance);
		$request->attributes->set('site', $currentSite);
		if(!empty($currentLocale))
			$request->setLocale($currentLocale);
	}
}