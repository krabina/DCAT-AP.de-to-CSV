<?php

// Autoload dependencies
require 'vendor/autoload.php';

use Symfony\Component\Console as Console;

/**
 * Start Script for Console Application
 *
 * @author Markus Wallisch <markus.wallisch@interactives.eu>
 */

$application = new Console\Application('DCAT-AP.de to CSV','0.1');
//$application->add(new Ives\HelloWorldCommand('hello:world'));
$application->add(new Ives\GetAllPackagesCommand('get:packages'));

// Wrap in interactive shell
$shell = new Console\Shell($application);
$shell->run();