<?php
require_once 'vendor/autoload.php';

use Sodalto\DtoGenerator\Commands\GenerateDtoCommand;
use Symfony\Component\Console\Application;

$application = new Application('Generate DTOs for array data structures.', '0.0.1');
// acceptance tests
$application->add(new GenerateDtoCommand());
$application->run();
