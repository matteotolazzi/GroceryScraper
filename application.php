<?php

//
// Run:
// php application.php scrapeGrocerySite
//
// Test:
// phpunit --bootstrap vendor/autoload.php tests
//

require './vendor/autoload.php';

use TechnicalTest\ScrapeCommand;
use Symfony\Component\Console\Application;

$application = new Application();

$application->add(new ScrapeCommand());

$application->run();