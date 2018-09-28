<?php

use Prodigious\MultisiteBundle\EventDispatcher\MultisiteDispatcher;
use Symfony\Component\HttpFoundation\Request;

require __DIR__.'/../../vendor/autoload.php';

define('PROJECT_ROOT', dirname(dirname(dirname(__FILE__))));

$dispacher = new MultisiteDispatcher(PROJECT_ROOT, Request::createFromGlobals());

$site = $dispacher->run();