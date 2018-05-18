<?php

use Prodigious\MultisiteBundle\EventDispatcher\MultisiteDispatcher;

require __DIR__.'/../../vendor/autoload.php';

define('PROJECT_ROOT', dirname(dirname(dirname(__FILE__))));

$dispacher = new MultisiteDispatcher(PROJECT_ROOT);

$dispacher->run();